<?php
/**
 * This file is part of the explorator project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: julien
 * Date: 18/05/2016
 * Time: 17:34
 */

namespace JMaitan\SonataMediaCropBundle\Cropper;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;

class Cropper implements CropperInterface
{
    protected $adapter;
    protected $mode;
    protected $metadata;

    /**
     * @param ImagineInterface $adapter
     * @param string           $mode
     */
    public function __construct(ImagineInterface $adapter, $mode, MetadataBuilderInterface $metadata)
    {
        $this->adapter = $adapter;
        $this->mode = $mode;
        $this->metadata = $metadata;
    }

    public function getSettings(MediaInterface $media, array $settings)
    {
        $width = $settings['width'];
        $height = $settings['height'];

        $cropperSettings = array();

        // make sure our arguments are valid
        if ((is_numeric($width) && $width > 0) && (is_numeric($height) && $height > 0)) {
            $cropperSettings['aspectRatio'] = $width/$height;
        }

        return $cropperSettings;
    }

    public function crop(MediaInterface $media, File $in, File $out, array $settings)
    {
        $box = new Box($settings['w'], $settings['h']);
        $cropPoint = new Point($settings['x'], $settings['y']);

        $image = $this->adapter->load($in->getContent());
        $content = $image
            ->thumbnail($media->getBox(), $this->mode)
            ->crop($cropPoint, $box)
            ->get($media->getExtension(), ['quality' => 100])
        ;

        $out->setContent($content, $this->metadata->get($media, $out->getName()));

        return $out;
    }
}
