# sonata-media-crop
A cropper for Sonata Media


**This is a WIP. It needs refactoring, improvements, etc...**


----------


How it works
------------

You must enable the extension on the admin you want to use it. For example, you could do in app/config/config.yml:

    sonata_admin:
        extensions:
            j_maitan_sonata_media_crop.crop.extension:
                implements:
                    - Sonata\MediaBundle\Model\MediaInterface

Once the extension is activated, you can access the cropper at the new route called "crop" provided by the extension with the following pattern: {id}/crop/{format}
For example, with a default sonata-media bundle installation, it give the following URL: /admin/sonata/media/media/2/crop/small

When your ready to crop your image, press the crop button in the right column. It will crop the image and generate the thumbnail for the format based on the cropped image instead of the reference image.
