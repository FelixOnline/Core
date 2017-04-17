#!/usr/bin/env php
<?php

function term($msg, $status = 0) {
    echo $msg."\n";
    exit($status);
}

function checkSyntax($fileName, $json) {
    $required = array(
        "class", "table", "dontLog", "ignoreCodeCoverage", "description", "author", "email", "license", "use", "fields", "includeFile"
    );

    $fieldRequired = array(
        "name", "type", "class", "description", "primary", "notnull", "transform_no_html", "dont_log"
    );

    $fieldTypes = array(
        "BooleanField", "CharField", "DateTimeField", "ForeignKey", "IntegerField", "TextField"
    );

    $error = 0;

    foreach($required as $req) {
        if(!array_key_exists($req, $json)) {
            echo $fileName.": Required element ".$req." is missing.\n";
            $error++;
        }
    }

    foreach($json['fields'] as $field) {
        foreach($fieldRequired as $req) {
            if(!array_key_exists($req, $field)) {
                echo $fileName."/".$field.": Required element ".$req." is missing.\n";
                $error++;
            }
        }

        if(!in_array($field['type'], $fieldTypes)) {
            echo $fileName."/".$field.": Invalid type ".$field['type'].".\n";
            $error++;
        }

        if($field['class'] && $field['type'] != "ForeignKey") {
            echo $fileName."/".$field.": Only set field class if a ForeignKey field.\n";
            $error++;
        }

        if(!$field['class'] && $field['type'] == "ForeignKey") {
            echo $fileName."/".$field.": Must set field class if a ForeignKey field.\n";
            $error++;
        }
    }

    if($json['includeFile'] && !file_exists(__DIR__ . '/includes/'.$json['class'].'.php')) {
        echo $fileName.": Include file specified but is not present (looking for ".__DIR__ . "/includes/".$json['class'].".php).\n";
        $error++;
    }

    if($json['includeFile'] && !$json['ignoreCodeCoverage']) {
        echo $fileName.": You may not opt the file out of code coverage statistics if an include file is used.\n";
        $error++;
    }

    return $error;
}

function makeFile($json) {
    $json['use'][] = 'FelixOnline\Base\BaseDB';
    $json['use'][] = 'FelixOnline\Base\BaseManager';
    $json['use'][] = 'FelixOnline\Base\Type';
    $json['use'][] = 'FelixOnline\Base\App';
    $json['use'][] = 'FelixOnline\Exceptions\InternalException';

    $pk = 'id';

    foreach($json['fields'] as $field) {
        if($field['primary']) {
            $pk = $field['name'];
        }
    }

    $string = array();
    $string[] = "<?php";
    $string[] = "namespace FelixOnline\Core;";
    $string[] = '';

    foreach($json['use'] as $use) {
        $string[] = 'use '.$use.';';
    }
    $string[] = '';

    $string[] = '/**';
    $string[] = ' * '.$json['description'];
    $string[] = ' *';
    $string[] = ' * Fields:';

    foreach($json['fields'] as $field) {
        $string[] = ' * - '.$field['name'].': ('.$field['type'].') '.$field['description'];
    }

    $string[] = ' *';
    $string[] = ' * @author '.$json['author'].' <'.$json['email'].'>';
    $string[] = ' * @license '.$json['license'];

    if($json['ignoreCodeCoverage']) {
        $string[] = ' * @codeCoverageIgnore';
    }

    $string[] = ' */';
    $string[] = 'class '.$json['class'].' extends BaseDB';
    $string[] = '{';
    $string[] = '    /** Table name */';
    $string[] = '    public $dbtable = \''.$json['table'].'\';';
    $string[] = '';
    $string[] = '    public function __construct($'.$pk.' = null)';
    $string[] = '    {';
    $string[] = '        $fields = array(';

    foreach($json['fields'] as $field) {
        if($field['primary'] || $field['notnull'] || $field['transform_no_html'] || $field['dont_log']) {
            if($field['class']) {
                $string[] = '            \''.$field['name'].'\' => new Type\\'.$field['type'].'(\''.$field['class'].'\', array(';
            } else {
                $string[] = '            \''.$field['name'].'\' => new Type\\'.$field['type'].'(null, array(';
            }

            if($field['primary']) {
                $string[] = '                "primary" => true,';
            }

            if($field['notnull']) {
                $string[] = '                "null" => false,';
            }

            if($field['dont_log']) {
                $string[] = '                "dont_log" => true,';
            }

            if($field['transform_no_html']) {
                $string[] = '                "transformers" => array(';
                $string[] = '                    Type\\BaseType::TRANSFORMER_NO_HTML';
                $string[] = '                ),';
            }

            $string[] = '            )),';
        } elseif($field['class']) {
            $string[] = '            \''.$field['name'].'\' => new Type\\'.$field['type'].'(\''.$field['class'].'\'),';
        } else {
            $string[] = '            \''.$field['name'].'\' => new Type\\'.$field['type'].'(),';
        }
    }

    $string[] = '        );';
    $string[] = '    }';

    if($json['includeFile']) {
        $string[] = '';
        $string[] = file_get_contents(__DIR__ . '/includes/'.$json['name'].'.php');
    }

    $string[] = '}';
    $string[] = '';

    file_put_contents(__DIR__ . '/built/'.$json['class'].'.php', implode($string, PHP_EOL));
}

if(php_sapi_name() !== "cli") {
    term("Run from CLI only.", 64);
}

echo "Model Builder\n";
echo "Step 1: Checking JSON... ";

$files = glob(__DIR__ . '/definitions/*.json');

if(!$files || count($files) == 0) {
    term('Could not find any files in the definitions folder.', 64);
}

$error = 0;

foreach($files as $file) {
    $data = json_decode(file_get_contents($file), true);

    if(!$data) {
        echo $file.": Cannot read JSON (check syntax)\n";
        $error++;
        continue;
    }

    $error += checkSyntax($file, $data);
}

if($error) {
    term('Please fix the errors above and try again.', 4);
}

echo "OK\n";

echo "Step 2: Building files... ";

foreach($files as $file) {
    $data = json_decode(file_get_contents($file), true);

    makeFile($data);
}

echo "OK\n";

term("All done!", 0);
