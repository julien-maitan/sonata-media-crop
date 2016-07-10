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
You can add the link on the meida edit page by overriding the edit template and changing the loop for format links (line 88) like this:

    {% for name, format in sonata_media.pool.formatNamesByContext(object.context) %}
     <tr>
         <th>
             <a href="{% path object, name %}" target="_blank">{{ name }}</a>
         </th>
         <td>
             <div class="form-group">
                 <div class="input-group">
                     <input type="text" class="form-control" onClick="this.select();" readonly="readonly" value="{% path object, name %}" />
                     <div class="input-group-btn">
                         <a href="{{ admin.generateUrl('crop', {'id': object|sonata_urlsafeid, 'format': name}) }}" class="btn btn-default"><i class="fa fa-fw fa-crop"></i></a>
                     </div>
                 </div>
             </div>
         </td>
     </tr>
    {% endfor %}

When your ready to crop your image, press the crop button in the right column. It will crop the image and generate the thumbnail for the format based on the cropped image instead of the reference image.

