SET IDENTITY_INSERT [#__content_types]  ON;

INSERT INTO [#__content_types] ([type_id], [type_title], [type_alias], [table], [field_mappings])
SELECT 16, 'Image', 'com_media.image', '{"special":{"dbtable":"#__ucm_content","key":"core_content_id","type":"Corecontent","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"core_content_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '{"common":{"core_content_item_id":"core_content_id","core_title":"core_title","core_state":"core_state","core_alias":"core_alias","core_created_time":"core_created_time","core_modified_time":"core_modified_time","core_body":"core_body", "core_hits":"core_hits","core_publish_up":"core_publish_up","core_publish_down":"core_publish_down","core_access":"core_access", "core_params":"core_params", "core_featured":"core_featured", "core_metadata":"core_metadata", "core_language":"core_language", "core_images":"core_images", "core_urls":"core_urls", "core_version":"core_version", "core_ordering":"core_ordering", "core_metakey":"core_metakey", "core_metadesc":"core_metadesc", "core_catid":"core_catid", "core_xreference":"core_xreference", "asset_id":"asset_id"}, "special":{"core_content_item_id":"core_content_id","core_title":"core_title","core_state":"core_state","core_alias":"core_alias","core_created_time":"core_created_time","core_modified_time":"core_modified_time","core_body":"core_body", "core_hits":"core_hits","core_publish_up":"core_publish_up","core_publish_down":"core_publish_down","core_access":"core_access", "core_params":"core_params", "core_featured":"core_featured", "core_metadata":"core_metadata", "core_language":"core_language", "core_images":"core_images", "core_urls":"core_urls", "core_version":"core_version", "core_ordering":"core_ordering", "core_metakey":"core_metakey", "core_metadesc":"core_metadesc", "core_catid":"core_catid", "core_xreference":"core_xreference", "asset_id":"asset_id"}}'
UNION ALL
SELECT 17, 'Media Category', 'com_media.category', '', '';

SET IDENTITY_INSERT #__content_types  OFF;

SET IDENTITY_INSERT [#__categories]  ON;

INSERT INTO [#__categories] ([id], [asset_id], [parent_id], [lft], [rgt], [level], [path], [extension], [title], [alias], [published], [access], [params], [metadata], [created_user_id], [hits], [language], [version])
SELECT 8, 33, 1, 13, 14, 1, 'uncategorised', 'com_media', 'Uncategorised', 'uncategorised', 1, 1, '{"category_layout":"","image":""}', '{"author":"","robots":""}', 42, 0, '*', 1;

SET IDENTITY_INSERT #__categories  OFF;


