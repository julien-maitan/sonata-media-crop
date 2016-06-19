<?php
/**
 * This file is part of the sonata-media-crop project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Created by PhpStorm.
 * User: julien
 * Date: 18/06/16
 * Time: 17:15
 */

namespace JMaitan\SonataMediaCropBundle\Cropper;


use Sonata\MediaBundle\Model\MediaInterface;

interface CropperInterface
{
    public function getSettings(MediaInterface $media, array $settings);
}