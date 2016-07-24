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
use Imagine\Image\Palette\ColorParser;
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

        if ((is_numeric($width) && $width > 0) && (is_numeric($height) && $height > 0)) {
            $cropperSettings['aspectRatio'] = $width/$height;
        }

        return $cropperSettings;
    }

    public function crop(MediaInterface $media, File $in, File $out, array $settings)
    {
        $image = $this->adapter->load($in->getContent());
        $thumb = $image->thumbnail($media->getBox(), $this->mode);

        if ($settings['scaleX'] != 1) {
            $thumb = $thumb->flipHorizontally();
        }

        if ($settings['scaleY'] != 1) {
            $thumb = $thumb->flipVertically();
        }

        if ($settings['r'] != 0) {
            $thumb = $thumb->rotate($settings['r']);
        }

        if ($settings['w'] != 0 && $settings['h'] != 0) {
            $box = new Box($settings['w'], $settings['h']);
            $cropPoint = new Point($settings['x'], $settings['y']);

            $thumb = $thumb->crop($cropPoint, $box);
        }

        $content = $thumb->get($media->getExtension(), ['quality' => 100]);

        $out->setContent($content, $this->metadata->get($media, $out->getName()));

        return $out;
    }
}
