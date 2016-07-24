<?php

namespace JMaitan\SonataMediaCropBundle\Controller;

use JMaitan\SonataMediaCropBundle\Resizer\CropResizerInterface;
use Sonata\MediaBundle\Controller\MediaAdminController as BaseMediaAdminController;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThan;


class CropController extends BaseMediaAdminController
{
    public function indexAction($id = null)
    {
        $request = $this->getRequest();
        $templateKey = 'crop';

        $id = $id ?: $request->get($this->admin->getIdParameter());
        /** @var MediaInterface $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->setSubject($object);

        $this->admin->checkAccess('edit', $object);

        $provider = $this->getProvider($object);
        $cropper = $this->container->get('j_maitan_sonata_media_crop.crop.cropper');
        $settings = $provider->getFormat($request->get('format'));
        $croppedFilename = 'reference';
        $cropSettings = array();

        if ('reference' !== ($format = $request->get('format', 'reference'))) {
            $croppedFilename = $format . '_tmp_cropped';
            $cropSettings = $cropper->getSettings($object, $settings);
        }

        $cropping = array(
            'x' => 0,
            'y' => 0,
            'w' => $object->getWidth(),
            'h' => $object->getHeight(),
            'r' => 0,
            'scaleX' => 0,
            'scaleY' => 0,
        );

        $form = $this->
        createFormBuilder($cropping)
            ->add('x', NumberType::class)
            ->add('y', NumberType::class)
            ->add('w', NumberType::class/*, array('constraints' => array(new GreaterThan('0')))*/)
            ->add('h', NumberType::class/*, array('constraints' => array(new GreaterThan('0')))*/)
            ->add('r', NumberType::class)
            ->add('scaleX', NumberType::class)
            ->add('scaleY', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cropped = $cropper->crop($object,
                $provider->getReferenceFile($object),
                $provider->getFilesystem()->get($provider->generatePrivateUrl($object, $croppedFilename), true),
                $form->getData()
            );

            if ('reference' !== $format) {
                $provider->getResizer()->resize(
                    $object,
                    $cropped,
                    $provider->getFilesystem()->get($provider->generatePrivateUrl($object, $format), true),
                    $object->getExtension(),
                    $settings
                );

                $cropped->delete();
            }

            $this->addFlash('sonata_flash_success', 'flash_crop_success');
            return $this->redirectTo($object);
        }


        return $this->render('JMaitanSonataMediaCropBundle:Crop:index.html.twig',
            array(
                'base_template' => $this->getBaseTemplate(),
                'action' => 'create',
                'object' => $object,
                'settings' => $cropSettings,
                'form' => $form->createView(),
        ));
    }

    public function getProvider(MediaInterface $media)
    {
        return $this->get('sonata.media.pool')->getProvider($media->getProviderName());
    }
}
