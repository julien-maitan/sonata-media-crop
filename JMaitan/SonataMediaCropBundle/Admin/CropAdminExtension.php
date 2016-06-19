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
 * Date: 17/06/16
 * Time: 22:48
 */

namespace JMaitan\SonataMediaCropBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class CropAdminExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add(
            'crop',
            $admin->getRouterIdParameter().'/crop/{format}',
            array(
                '_controller' => 'JMaitanSonataMediaCropBundle:Crop:index',
                'format' => 'reference',
            )
        );
    }
}