<?php

use Phinx\Migration\AbstractMigration;

class MediaCleanup extends AbstractMigration
{
    public function up()
    {
        $this->dropTable('media_video');
        $this->dropTable('media_photo_images');
        $this->dropTable('media_photo_image');
        $this->dropTable('media_photo_albums');
        $this->dropTable('media_photo_album');
    }

    public function down()
    {
        $table = $this->table('media_photo_album');
        $table->addColumn('folder', 'text')
              ->addColumn('title', 'string', array('length' => 64))
              ->addColumn('author', 'string', array('length' => 100))
              ->addColumn('date', 'date')
              ->addColumn('description', 'text')
              ->addColumn('order', 'integer', array('length' => 6))
              ->addColumn('visible', 'boolean', array('default' => 1))
              ->addColumn('thumbnail', 'integer', array('length' => 10))
              ->addColumn('hits', 'integer')
              ->create();

        $table = $this->table('media_photo_albums');
        $table->addColumn('albumFolder', 'text')
              ->addColumn('albumTitle', 'string', array('length' => 64))
              ->addColumn('albumAuthor', 'string', array('length' => 100))
              ->addColumn('albumDate', 'date')
              ->addColumn('albumDesc', 'text')
              ->addColumn('albumOrder', 'integer', array('length' => 6))
              ->addColumn('visible', 'boolean', array('default' => 1))
              ->addColumn('albumThumb', 'integer', array('length' => 10))
              ->addColumn('hits', 'integer')
              ->create();

        $table = $this->table('media_photo_image');
        $table->addColumn('album_id', 'text')
              ->addColumn('name', 'integer')
              ->addColumn('date', 'text')
              ->addColumn('title', 'text')
              ->addColumn('caption', 'text')
              ->addColumn('camera', 'text')
              ->addColumn('iso', 'integer', array('length' => 6))
              ->addColumn('fstop', 'string', array('length' => 5))
              ->addColumn('orientation', 'integer', array('length' => 2))
              ->addColumn('tags', 'string', array('length' => 100))
              ->addColumn('geo_coords', 'string', array('length' => 32))
              ->create();

        $table = $this->table('media_photo_images');
        $table->addColumn('albumId', 'text')
              ->addColumn('imageName', 'integer')
              ->addColumn('imageDate', 'text')
              ->addColumn('imageTitle', 'text')
              ->addColumn('imageCaption', 'text')
              ->addColumn('camera', 'text')
              ->addColumn('iso', 'integer', array('length' => 6))
              ->addColumn('fstop', 'string', array('length' => 5))
              ->addColumn('orientation', 'integer', array('length' => 2))
              ->addColumn('tags', 'string', array('length' => 100))
              ->addColumn('geoCoords', 'string', array('length' => 32))
              ->create();

        $table = $this->table('media_video');
        $table->addColumn('title', 'string', array('length' => 100))
              ->addColumn('description', 'text')
              ->addColumn('author', 'string', array('length' => 1000))
              ->addColumn('video_id', 'string', array('length' => 30))
              ->addColumn('date', 'date')
              ->addColumn('hidden', 'integer')
              ->addColumn('hits', 'integer')
              ->addColumn('site', 'string', array('length' => 30, 'comment' => 'Name of site the video is on'))
              ->addColumn('thumbnail', 'integer', array('length' => 100))
              ->create();
    }
}
