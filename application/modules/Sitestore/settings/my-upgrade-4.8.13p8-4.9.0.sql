--
-- Updating data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"sitestoreproduct_general","action":"home","icon":"fa-shopping-basket"}'
WHERE `name` = 'core_main_sitestoreproduct';

--
-- Change the Commentable & Shareable values
--
UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE type='comment_sitestore_store' and module='sitestore';

UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE (type='comment_sitestoreproduct_photo' or type = 'comment_sitestoreproduct_product' or type = 'comment_sitestoreproduct_review' or type = 'comment_sitestoreproduct_video') and module='sitestoreproduct';