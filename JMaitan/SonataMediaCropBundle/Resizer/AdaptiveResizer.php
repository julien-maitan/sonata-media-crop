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

namespace JMaitan\SonataMediaCropBundle\Resizer;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;

class AdaptiveResizer implements ResizerInterface
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

    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        if (!(isset($settings['width']) && $settings['width'])) {
            throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getProviderName()));
        }

        $targetSize = $this->getBox($media, $settings);
        $image = $this->adapter->load($in->getContent());

        // Get the highest ratio to which scale the image
        $ratio = max($targetSize->getWidth() / $media->getWidth(), $targetSize->getHeight() / $media->getHeight());
        $thumBox = $media->getBox()->scale($ratio);

        // Get the starting point for cropping
        $x = max(($thumBox->getWidth() - $targetSize->getWidth()) / 2, 0);
        $y = max(($thumBox->getHeight() - $targetSize->getHeight()) / 2, 0);
        $cropPoint = new Point($x, $y);

        $content = $image
            ->thumbnail($media->getBox(), $this->mode)
            ->resize($thumBox)
            ->crop($cropPoint, $targetSize)
            ->get($format, ['quality' => $settings['quality']]);

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getBox(MediaInterface $media, array $settings)
    {
        $width = $settings['width'];
        $height = $settings['height'];

        // make sure our arguments are valid
        if ((!is_numeric($width) || $width  == 0) && (!is_numeric($height) || $height == 0)) {
            throw new \RuntimeException(sprintf('Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.', $media->getContext(), $media->getProviderName()));
        }

        if (!is_numeric($width) || $width  == 0) {
            $width = intval(($height * $media->getBox()->getWidth()) / $media->getBox()->getHeight());
        }

        if (!is_numeric($height) || $height  == 0) {
            $height = intval(($width * $media->getBox()->getHeight()) / $media->getBox()->getWidth());
        }

        // make sure we're not exceeding our image size if we're not supposed to
        /*if ($settings['upscale'] === false) {
            $maxHeight = ($height > $media->getBox()->getHeight()) ? $media->getBox()->getHeight() : $height;
            $maxWidth  = ($width > $media->getBox()->getWidth()) ? $media->getBox()->getWidth() : $width;
        } else {
            $maxHeight = $height;
            $maxWidth  = $width;
        }*/

        return new Box($width, $height);
    }
}
