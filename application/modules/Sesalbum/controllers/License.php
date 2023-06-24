<?php

//folder name or directory name.
$module_name = 'sesalbum';
//product title and module title.
$module_title = 'Advanced Photos & Albums Plugin';
if (!$this->getRequest()->isPost()) {
  return;
}
if (!$form->isValid($this->getRequest()->getPost())) {
  return;
}
if ($this->getRequest()->isPost()) {
  $postdata = array();
//domain name
  $postdata['domain_name'] = $_SERVER['HTTP_HOST'];
//license key
  $postdata['licenseKey'] = @base64_encode($_POST['sesalbum_licensekey']);
  $postdata['module_title'] = @base64_encode($module_title);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://socialnetworking.solutions/licensecheck.php");
  curl_setopt($ch, CURLOPT_POST, 1);
// in real life you should use something like:
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
// receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec($ch);
  $error = 0;
  if (curl_error($ch)) {
    $error = 1;
  }
  curl_close($ch);
//here we can set some variable for checking in plugin files.
  if ($server_output == "OK" && $error != 1) {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.pluginactivated')) {
      //notification type
	//notification type
	$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
	("sesalbum_favouritephoto", "sesalbum", \'{item:$subject} favourite your {item:$object:photo}.\', "0", "", "1"),
	("sesalbum_favouritealbum", "sesalbum", \'{item:$subject} favourite your {item:$object:album}.\', "0", "", "1"),
	("sesalbum_albumrated", "sesalbum", \'{item:$subject} rate your {item:$object:album}.\', "0", "", "1"),
	("sesalbum_photorated", "sesalbum", \'{item:$subject} rate your {item:$object:photo}.\', "0", "", "1");');
      //email template
      $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_sesalbum_favouritephoto', 'sesalbum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),('notify_sesalbum_favouritealbum', 'sesalbum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),('notify_sesalbum_photorated', 'sesalbum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),('notify_sesalbum_albumrated', 'sesalbum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");
      $table_exist_albums = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
      if (empty($table_exist_albums)) {
        $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_albums` (
        `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(128) NOT NULL,
        `description` mediumtext NOT NULL,
        `owner_type` varchar(64) NOT NULL,
        `owner_id` int(11) unsigned NOT NULL,
        `category_id` int(11) unsigned NOT NULL DEFAULT "0",
				`subcat_id` int(11) unsigned NOT NULL DEFAULT "0",
				`subsubcat_id` int(11) unsigned NOT NULL DEFAULT "0",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `photo_id` int(11) unsigned NOT NULL DEFAULT "0",
        `view_count` int(11) unsigned NOT NULL DEFAULT "0",
        `like_count` int(11) unsigned NOT NULL DEFAULT "0",
        `comment_count` int(11) unsigned NOT NULL DEFAULT "0",
        `search` tinyint(1) NOT NULL DEFAULT "1",
        `type` enum("wall","profile","message","blog") DEFAULT NULL,
        `is_featured` tinyint(1) NOT NULL DEFAULT "0",
        `is_sponsored` tinyint(1) NOT NULL DEFAULT "0",
				`offtheday` tinyint(1)	NOT NULL DEFAULT "0",
				`starttime` DATE DEFAULT NULL,
				`endtime` DATE DEFAULT NULL,
				`position_cover` VARCHAR(255) NULL,
				`art_cover` int(11) unsigned NOT NULL DEFAULT "0",
        PRIMARY KEY (`album_id`),
        KEY `owner_type` (`owner_type`,`owner_id`),
        KEY `category_id` (`category_id`),
        KEY `search` (`search`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      }
      $table_exist_photo = $db->query('SHOW TABLES LIKE \'engine4_album_photos\'')->fetch();
      if (empty($table_exist_photo)) {
        $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_photos` (
        `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `album_id` int(11) unsigned NOT NULL,
        `title` varchar(128) NOT NULL,
        `description` mediumtext NOT NULL,
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `order` int(11) unsigned NOT NULL DEFAULT "0",
        `owner_type` varchar(64) NOT NULL,
        `owner_id` int(11) unsigned NOT NULL,
        `file_id` int(11) unsigned NOT NULL,
        `view_count` int(11) unsigned NOT NULL DEFAULT "0",
        `like_count` int(10) unsigned NOT NULL DEFAULT "0",
        `comment_count` int(11) unsigned NOT NULL DEFAULT "0",
        `is_featured` tinyint(1) NOT NULL DEFAULT "0",
        `is_sponsored` tinyint(1) NOT NULL DEFAULT "0",
				`offtheday` tinyint(1)	NOT NULL DEFAULT "0",
				`starttime` DATE DEFAULT NULL,
				`endtime` DATE DEFAULT NULL,
        PRIMARY KEY (`photo_id`),
        KEY `album_id` (`album_id`),
        KEY `owner_type` (`owner_type`,`owner_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      }
    $catgoryData = array('0' => array('Other', 'others.jpg', 'other.png','Vivamus in quam ullamcorper, blandit turpis sed, congue purus. Quisque ultricies diam arcu, id blandit ipsum feugiat ut. Maecenas tincidunt nibh lorem, vitae consequat enim blandit volutpat. Donec vel velit nulla. Duis nunc libero, pretium eu sollicitudin vel, molestie tempus erat. Nulla non velit eu eros vestibulum suscipit. Nulla eget pellentesque magna, at imperdiet odio. Ut quis malesuada sapien. Maecenas vitae augue elementum, blandit sapien ultricies, vulputate erat. Duis elementum faucibus augue id aliquam. Mauris iaculis leo quis nunc blandit hendrerit. Proin ut libero tristique, interdum diam ac, pharetra metus. Mauris rhoncus ipsum dignissim ex ullamcorper, sed fringilla augue euismod. In volutpat, sem venenatis tristique hendrerit, sem lorem molestie ipsum, vel lobortis dolor enim efficitur augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'),array('Technology', 'technology.jpg', 'technology.png','We can change the World with Technology !!<br><br>Pellentesque lacinia hendrerit leo, nec hendrerit magna porttitor at. Vestibulum pellentesque erat orci, non mollis purus ornare a. Ut a blandit dolor. Quisque ac pharetra ex. Aliquam pretium pharetra elementum. Phasellus nec mollis metus, non pellentesque purus. Vivamus in sem facilisis, dictum ex suscipit, imperdiet tortor. Sed varius massa ex, quis porta elit interdum non. Mauris at dictum nisi. Maecenas malesuada diam sit amet turpis porttitor, ut aliquam nibh facilisis. Ut sit amet ligula lacus.<br><br>In hac habitasse platea dictumst. Cras mollis sagittis feugiat. Nunc ac velit eu turpis congue lobortis. Pellentesque quam diam, feugiat vitae ipsum sit amet, aliquet vestibulum ligula. Sed nulla risus, malesuada blandit egestas vel, semper a risus. Pellentesque et tincidunt mauris. Nunc sodales diam dictum, sollicitudin leo nec, dapibus sapien. Suspendisse a fringilla urna. Quisque luctus neque tristique, cursus nulla ac, egestas felis. Proin dapibus condimentum posuere. Aenean lacinia volutpat convallis. In gravida, elit eu imperdiet venenatis, lacus risus venenatis quam, at consectetur tortor tortor malesuada enim. Nam hendrerit ipsum vel odio molestie rutrum. Vivamus vitae risus eget est vehicula consequat. In varius nec dolor eu aliquet. '),array('Entertainment', 'entertainment.jpg', 'entertaintment.png','The Entertainment is in the Presentation !!<br><br> Pellentesque lacinia hendrerit leo, nec hendrerit magna porttitor at. Vestibulum pellentesque erat orci, non mollis purus ornare a. Ut a blandit dolor. Quisque ac pharetra ex. Aliquam pretium pharetra elementum. Phasellus nec mollis metus, non pellentesque purus. Vivamus in sem facilisis, dictum ex suscipit, imperdiet tortor. Sed varius massa ex, quis porta elit interdum non. Mauris at dictum nisi. Maecenas malesuada diam sit amet turpis porttitor, ut aliquam nibh facilisis. Ut sit amet ligula lacus.<br><br>In hac habitasse platea dictumst. Cras mollis sagittis feugiat. Nunc ac velit eu turpis congue lobortis. Pellentesque quam diam, feugiat vitae ipsum sit amet, aliquet vestibulum ligula. Sed nulla risus, malesuada blandit egestas vel, semper a risus. Pellentesque et tincidunt mauris. Nunc sodales diam dictum, sollicitudin leo nec, dapibus sapien. Suspendisse a fringilla urna. Quisque luctus neque tristique, cursus nulla ac, egestas felis. Proin dapibus condimentum posuere. Aenean lacinia volutpat convallis. In gravida, elit eu imperdiet venenatis, lacus risus venenatis quam, at consectetur tortor tortor malesuada enim. Nam hendrerit ipsum vel odio molestie rutrum. Vivamus vitae risus eget est vehicula consequat. In varius nec dolor eu aliquet. '),array('Family & Home', 'family-home.jpg', 'home.png','Family - Where Life Begins & Love Never Ends !!<br><br>Vivamus in quam ullamcorper, blandit turpis sed, congue purus. Quisque ultricies diam arcu, id blandit ipsum feugiat ut. Maecenas tincidunt nibh lorem, vitae consequat enim blandit volutpat. Donec vel velit nulla. Duis nunc libero, pretium eu sollicitudin vel, molestie tempus erat. Nulla non velit eu eros vestibulum suscipit. Nulla eget pellentesque magna, at imperdiet odio. Ut quis malesuada sapien. Maecenas vitae augue elementum, blandit sapien ultricies, vulputate erat. Duis elementum faucibus augue id aliquam. Mauris iaculis leo quis nunc blandit hendrerit. Proin ut libero tristique, interdum diam ac, pharetra metus. Mauris rhoncus ipsum dignissim ex ullamcorper, sed fringilla augue euismod. In volutpat, sem venenatis tristique hendrerit, sem lorem molestie ipsum, vel lobortis dolor enim efficitur augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'),array('Recreation', 'recreation.jpg', 'Recreation.png','Vivamus in quam ullamcorper, blandit turpis sed, congue purus. Quisque ultricies diam arcu, id blandit ipsum feugiat ut. Maecenas tincidunt nibh lorem, vitae consequat enim blandit volutpat. Donec vel velit nulla. Duis nunc libero, pretium eu sollicitudin vel, molestie tempus erat. Nulla non velit eu eros vestibulum suscipit. Nulla eget pellentesque magna, at imperdiet odio. Ut quis malesuada sapien. Maecenas vitae augue elementum, blandit sapien ultricies, vulputate erat. Duis elementum faucibus augue id aliquam. Mauris iaculis leo quis nunc blandit hendrerit. Proin ut libero tristique, interdum diam ac, pharetra metus. Mauris rhoncus ipsum dignissim ex ullamcorper, sed fringilla augue euismod. In volutpat, sem venenatis tristique hendrerit, sem lorem molestie ipsum, vel lobortis dolor enim efficitur augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'),array('Personal', 'personal.jpg', 'personal.png','The authentic self is a soul made visible.<br><br>Vivamus in quam ullamcorper, blandit turpis sed, congue purus. Quisque ultricies diam arcu, id blandit ipsum feugiat ut. Maecenas tincidunt nibh lorem, vitae consequat enim blandit volutpat. Donec vel velit nulla. Duis nunc libero, pretium eu sollicitudin vel, molestie tempus erat. Nulla non velit eu eros vestibulum suscipit. Nulla eget pellentesque magna, at imperdiet odio. Ut quis malesuada sapien. Maecenas vitae augue elementum, blandit sapien ultricies, vulputate erat. Duis elementum faucibus augue id aliquam. Mauris iaculis leo quis nunc blandit hendrerit. Proin ut libero tristique, interdum diam ac, pharetra metus. Mauris rhoncus ipsum dignissim ex ullamcorper, sed fringilla augue euismod. In volutpat, sem venenatis tristique hendrerit, sem lorem molestie ipsum, vel lobortis dolor enim efficitur augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. '),array('Health', 'health.jpg', 'health.png','The Greatest Wealth is HEALTH !!<br><br>Vivamus in quam ullamcorper, blandit turpis sed, congue purus. Quisque ultricies diam arcu, id blandit ipsum feugiat ut. Maecenas tincidunt nibh lorem, vitae consequat enim blandit volutpat. Donec vel velit nulla. Duis nunc libero, pretium eu sollicitudin vel, molestie tempus erat. Nulla non velit eu eros vestibulum suscipit. Nulla eget pellentesque magna, at imperdiet odio. Ut quis malesuada sapien. Maecenas vitae augue elementum, blandit sapien ultricies, vulputate erat. Duis elementum faucibus augue id aliquam. Mauris iaculis leo quis nunc blandit hendrerit. Proin ut libero tristique, interdum diam ac, pharetra metus. Mauris rhoncus ipsum dignissim ex ullamcorper, sed fringilla augue euismod. In volutpat, sem venenatis tristique hendrerit, sem lorem molestie ipsum, vel lobortis dolor enim efficitur augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. '),array('Society', 'society.jpg', 'society.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pretium magna ut efficitur viverra. Etiam pellentesque vulputate mauris quis finibus. Vestibulum condimentum auctor mi, tincidunt molestie nunc. Aliquam nisi metus, ornare vel eros et, dictum luctus diam. Nunc orci massa, finibus consectetur leo vitae, sollicitudin vulputate orci. Donec et tortor varius, placerat tortor sit amet, vulputate justo. Etiam ex tortor, venenatis eu augue eu, vulputate varius augue. In convallis condimentum sapien, id lacinia ex volutpat ut. Maecenas pulvinar lorem lorem, eget iaculis nibh commodo sit amet.<br><br>Cras diam nisi, consequat vitae mauris vitae, mattis sagittis dui. Nullam nec est in urna congue fringilla at nec urna. Sed facilisis eros nec magna tempus fringilla. Cras sagittis lacus eget condimentum auctor. Donec vitae ante varius ex pellentesque ornare. Donec scelerisque porttitor erat, sit amet rutrum leo pretium id. Pellentesque finibus volutpat pharetra. Mauris vitae molestie sem, aliquet dapibus turpis. Integer vel sodales justo, at tempor turpis.'),array('Shopping', 'shopping.jpg', 'shopping.png','Show the Fashion, after SHOPPING.<br><br> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pretium magna ut efficitur viverra. Etiam pellentesque vulputate mauris quis finibus. Vestibulum condimentum auctor mi, tincidunt molestie nunc. Aliquam nisi metus, ornare vel eros et, dictum luctus diam. Nunc orci massa, finibus consectetur leo vitae, sollicitudin vulputate orci. Donec et tortor varius, placerat tortor sit amet, vulputate justo. Etiam ex tortor, venenatis eu augue eu, vulputate varius augue. In convallis condimentum sapien, id lacinia ex volutpat ut. Maecenas pulvinar lorem lorem, eget iaculis nibh commodo sit amet.<br><br>Cras diam nisi, consequat vitae mauris vitae, mattis sagittis dui. Nullam nec est in urna congue fringilla at nec urna. Sed facilisis eros nec magna tempus fringilla. Cras sagittis lacus eget condimentum auctor. Donec vitae ante varius ex pellentesque ornare. Donec scelerisque porttitor erat, sit amet rutrum leo pretium id. Pellentesque finibus volutpat pharetra. Mauris vitae molestie sem, aliquet dapibus turpis. Integer vel sodales justo, at tempor turpis. '),array('Business', 'business.jpg', 'business.png','Business is a combination of War and Sport.<br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('Arts & Culture', 'art-culture.jpg', 'art.png','Culture is the Arts elevated to a set of BELIEFS - Thomas Wolfe.<br><br>Aliquam pulvinar urna sed sapien finibus, at bibendum lacus ullamcorper. Quisque volutpat porta libero ut dignissim. Pellentesque velit est, tincidunt vel arcu eget, dignissim pulvinar erat. Vestibulum eleifend et dui quis commodo. Maecenas feugiat est tincidunt turpis ornare hendrerit. Fusce sit amet facilisis ipsum. Ut dignissim pellentesque erat, sed eleifend sem tristique sed. Duis et rutrum velit, quis egestas purus. Aliquam sed quam odio.<br><br>Aliquam pulvinar urna sed sapien finibus, at bibendum lacus ullamcorper. Quisque volutpat porta libero ut dignissim. Pellentesque velit est, tincidunt vel arcu eget, dignissim pulvinar erat. Vestibulum eleifend et dui quis commodo. Maecenas feugiat est tincidunt turpis ornare hendrerit. Fusce sit amet facilisis ipsum. Ut dignissim pellentesque erat, sed eleifend sem tristique sed. Duis et rutrum velit, quis egestas purus. Aliquam sed quam odio.'),array('Sports', 'sport.jpg', 'sport.png','Life is a Sport, Make it Count !!<br><br> Pellentesque lacinia hendrerit leo, nec hendrerit magna porttitor at. Vestibulum pellentesque erat orci, non mollis purus ornare a. Ut a blandit dolor. Quisque ac pharetra ex. Aliquam pretium pharetra elementum. Phasellus nec mollis metus, non pellentesque purus. Vivamus in sem facilisis, dictum ex suscipit, imperdiet tortor. Sed varius massa ex, quis porta elit interdum non. Mauris at dictum nisi. Maecenas malesuada diam sit amet turpis porttitor, ut aliquam nibh facilisis. Ut sit amet ligula lacus.<br><br>In hac habitasse platea dictumst. Cras mollis sagittis feugiat. Nunc ac velit eu turpis congue lobortis. Pellentesque quam diam, feugiat vitae ipsum sit amet, aliquet vestibulum ligula. Sed nulla risus, malesuada blandit egestas vel, semper a risus. Pellentesque et tincidunt mauris. Nunc sodales diam dictum, sollicitudin leo nec, dapibus sapien. Suspendisse a fringilla urna. Quisque luctus neque tristique, cursus nulla ac, egestas felis. Proin dapibus condimentum posuere. Aenean lacinia volutpat convallis. In gravida, elit eu imperdiet venenatis, lacus risus venenatis quam, at consectetur tortor tortor malesuada enim. Nam hendrerit ipsum vel odio molestie rutrum. Vivamus vitae risus eget est vehicula consequat. In varius nec dolor eu aliquet.'));
$Sports = array('0'=>array('Football','football.jpg','football.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('Cricket','cricket.jpg','cricket.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('Basketball','basketball.jpg','basketball.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'));
$cricket = array('0'=>array('Premier League','premier-league.jpg','premier-league.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('ODI','odi.jpg','odi.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('World Cup','world-cup.jpg','world-cup.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'));
$ArtsCulture = array('0'=>array('Folk Dance','folk-dance.jpg','folk-dance.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Iconography','iconography.jpg','iconography.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Architecture','architecture.jpg','architecture.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'));
$Health = array('0'=>array('Health Supplements','health-supplements.jpg','health-supplements.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Excercise & Yoga','excercise-yoga.jpg','excercise-yoga.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Fruits & Vegetables','fruits-vegetables.jpg','fruits-vegetables.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '));
$FamilyHome = array('0'=>array('Photography','photography.jpg','photography.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'),array('Baby & Nursery','baby-nursery.jpg','baby-nursery.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Interior Designs','interior-designs.jpg','interior-designs.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '));
$Technology = array('0'=>array('Sci Fi','sci-fi.jpg','sci-fi.png',' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut dictum ex eu dignissim aliquam. Integer condimentum maximus lectus. Vestibulum vehicula ultricies viverra. Suspendisse semper libero ac arcu bibendum, nec tincidunt erat congue. Mauris nec nulla id ante facilisis maximus. Integer ut placerat leo. Donec et eros quis neque tincidunt aliquam. Cras id leo malesuada est faucibus aliquet et at sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus eget nisl a ligula fringilla commodo. Sed eget dictum tellus, in vehicula magna.<br><br>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque vel justo orci. Cras non orci vel dui vulputate hendrerit. Morbi aliquet, dolor a commodo consequat, nibh est sodales orci, sit amet euismod dui purus sed lorem. Sed iaculis nisl id facilisis luctus. Integer egestas dolor eu nulla varius vulputate. Duis et viverra lacus. Integer tincidunt vitae neque nec venenatis. Ut non leo id ante imperdiet bibendum pharetra a tellus. '),array('Innovation','innovation.jpg','innovation.png',' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut dictum ex eu dignissim aliquam. Integer condimentum maximus lectus. Vestibulum vehicula ultricies viverra. Suspendisse semper libero ac arcu bibendum, nec tincidunt erat congue. Mauris nec nulla id ante facilisis maximus. Integer ut placerat leo. Donec et eros quis neque tincidunt aliquam. Cras id leo malesuada est faucibus aliquet et at sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus eget nisl a ligula fringilla commodo. Sed eget dictum tellus, in vehicula magna.<br><br>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque vel justo orci. Cras non orci vel dui vulputate hendrerit. Morbi aliquet, dolor a commodo consequat, nibh est sodales orci, sit amet euismod dui purus sed lorem. Sed iaculis nisl id facilisis luctus. Integer egestas dolor eu nulla varius vulputate. Duis et viverra lacus. Integer tincidunt vitae neque nec venenatis. Ut non leo id ante imperdiet bibendum pharetra a tellus. '),array('Space','space.jpg','space.png',' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut dictum ex eu dignissim aliquam. Integer condimentum maximus lectus. Vestibulum vehicula ultricies viverra. Suspendisse semper libero ac arcu bibendum, nec tincidunt erat congue. Mauris nec nulla id ante facilisis maximus. Integer ut placerat leo. Donec et eros quis neque tincidunt aliquam. Cras id leo malesuada est faucibus aliquet et at sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus eget nisl a ligula fringilla commodo. Sed eget dictum tellus, in vehicula magna.<br><br>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque vel justo orci. Cras non orci vel dui vulputate hendrerit. Morbi aliquet, dolor a commodo consequat, nibh est sodales orci, sit amet euismod dui purus sed lorem. Sed iaculis nisl id facilisis luctus. Integer egestas dolor eu nulla varius vulputate. Duis et viverra lacus. Integer tincidunt vitae neque nec venenatis. Ut non leo id ante imperdiet bibendum pharetra a tellus. '));
$SciFi = array('0'=>array('Computers & Internet','computers-internet.jpg','computers-internet.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Earth & Space','earth-space.jpg','earth-space.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus. '),array('Supernatural Fiction','supernatural-fiction.jpg','supernatural-fiction.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sodales, mauris vitae fermentum suscipit, justo ex varius lorem, eget fermentum orci ligula ut massa. Etiam rhoncus imperdiet metus. In suscipit, enim ut tristique venenatis, dolor risus rutrum ex, et ultrices diam ante id ligula. Nulla suscipit, est eget mollis posuere, libero nulla accumsan augue, ut semper sapien ipsum mollis arcu. In mattis semper neque. Maecenas sollicitudin tempor ultrices. Donec orci arcu, lacinia sit amet rhoncus at, scelerisque at sem. Aenean auctor magna dui, ut ultrices dui volutpat in. Suspendisse potenti. Nam rutrum dolor at nibh mattis, eget efficitur libero pretium. Sed fermentum porta dui, vitae porta ante blandit eget. Phasellus aliquam nec erat sit amet tempus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam varius, ante vel tincidunt dictum, mi est commodo risus, sed ornare erat nulla ac purus.'));
      $table_exist_categories = $db->query('SHOW TABLES LIKE \'engine4_album_categories\'')->fetch();
      if (empty($table_exist_categories)) {
        $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_categories` (
          `category_id` int(11) unsigned NOT NULL auto_increment,
          `slug` varchar(255) NOT NULL,
          `category_name` varchar(128) NOT NULL,
          `subcat_id` int(11)  NULL DEFAULT 0,
          `subsubcat_id` int(11)  NULL DEFAULT 0,
          `title` varchar(255) DEFAULT NULL,
          `description` text,
          `thumbnail` int(11) NOT NULL DEFAULT 0,
          `cat_icon` int(11) NOT NULL DEFAULT 0,
          `order` int(11) NOT NULL DEFAULT 0,
          `profile_type` int(11) DEFAULT NULL,
          PRIMARY KEY (`category_id`),
          KEY `category_id` (`category_id`,`category_name`),
          KEY `category_name` (`category_name`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1');
        foreach ($catgoryData as $key => $value) {
          //Upload categories icon
          $db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "',0,0,'','".$value[3]."')");
          $catId = $db->lastInsertId();
          $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
          if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
            $cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $catId);
          else
            $cat_icon = 0;
          if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
            $thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $catId, true);
          else
            $thumbnail_icon = 0;
          $db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $catId);
					$valueName = str_replace(array(' ','&'),array('',''),$value[0]);
					if(isset(${$valueName})){
						foreach(${$valueName} as $value){
							$db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','".$catId."',0,'','".$value[3]."')");
							$subId = $db->lastInsertId();
							$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
							if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
								$cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $subId);
							else
								$cat_icon = 0;
							if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
								$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $subId, true);
							else
								$thumbnail_icon = 0;
							$db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subId);
							$valueSubName = str_replace(array(' ','&'),array('',''),$value[0]);
							if(isset(${$valueSubName})){
								foreach(${$valueSubName} as $value){
									$db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','0','".$catId."','','".$value[3]."')");
									$subsubId = $db->lastInsertId();
									$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
									if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
										$cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $subsubId);
									else
										$cat_icon = 0;
									if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
										$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $subsubId, true);
									else
										$thumbnail_icon = 0;
									$db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subsubId);
								}
							}
						}
					}	
          $runInstallCategory = true;
        }
      }
      $table_exist_categories = $db->query('SHOW TABLES LIKE \'engine4_album_categories\'')->fetch();
      if (!empty($table_exist_categories)) {
        $description = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'description\'')->fetch();
        if (empty($description)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `description` text ;");
        }
        $order = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'order\'')->fetch();
        if (empty($order)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `order` INT(11) NOT NULL DEFAULT 0 ;");
        }
        $title = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'title\'')->fetch();
        if (empty($title)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `title` VARCHAR( 255 ) NOT NULL ;");
        }
        $slug = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'slug\'')->fetch();
        if (empty($slug)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `slug` VARCHAR( 255 ) NOT NULL ;");
        }
        $subcat_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'subcat_id\'')->fetch();
        if (empty($subcat_id)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `subcat_id` INT( 11 )  NULL DEFAULT '0';");
        }
        $subsubcat_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'subsubcat_id\'')->fetch();
        if (empty($subsubcat_id)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `subsubcat_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
        $category_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'category_id\'')->fetch();
        if (empty($category_id)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `category_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
        $thumbnail = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'thumbnail\'')->fetch();
        if (empty($thumbnail)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `thumbnail` INT( 11 ) NOT NULL DEFAULT 0 ;");
        }
        $cat_icon = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'cat_icon\'')->fetch();
        if (empty($cat_icon)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `cat_icon` INT( 11 ) NOT NULL DEFAULT 0 ;");
        }
        $profile_type = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'profile_type\'')->fetch();
        if (empty($profile_type)) {
          $db->query("ALTER TABLE `engine4_album_categories` ADD `profile_type` INT( 11 ) NULL ;");
        }
        $db->query("UPDATE `engine4_album_categories` set `title` = category_name where title = ''");
        $db->query("UPDATE `engine4_album_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-')) where slug = ''");
        $db->query("UPDATE `engine4_album_categories` SET `order` = `category_id` WHERE `order` = 0");
      }
      if (empty($runInstallCategory)) {
        foreach ($catgoryData as $key => $value) {
          //Upload categories icon
          $catId = $db->query("SELECT category_id,thumbnail,cat_icon FROM `engine4_album_categories` WHERE category_name = '" . $value[0] . "'")->fetchAll();
          if (empty($catId[0]['category_id'])){
						$db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "',0,0,'','".$value[3]."')");
          	$catId = $db->lastInsertId();
					}else if(empty($catId[0]['thumbnail']) && empty($catId[0]['cat_icon']) && ($catId[0]['thumbnail'] == 0) && ($catId[0]['cat_icon'] == 0)){
						$catId = $catId[0]['category_id'];
					}else
						continue;
          $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
          if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
            $cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $catId);
          else
            $cat_icon = 0;
          if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
            $thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $catId, true);
          else
            $thumbnail_icon = 0;
          $db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $catId);
					$valueName = str_replace(array(' ','&'),array('',''),$value[0]);
					if(isset(${$valueName})){
						foreach(${$valueName} as $value){
							$db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','".$catId."',0,'','".$value[3]."')");
							$subId = $db->lastInsertId();
							$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
							if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
								$cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $subId);
							else
								$cat_icon = 0;
							if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
								$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $subId, true);
							else
								$thumbnail_icon = 0;
							$db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subId);
							$valueSubName = str_replace(array(' ','&'),array('',''),$value[0]);
							if(isset(${$valueSubName})){
								foreach(${$valueSubName} as $value){
									$db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`,`subcat_id`,`subsubcat_id`,`slug`,`description`) VALUES ( '" . $value[0] . "','0','".$catId."','','".$value[3]."')");
									$subsubId = $db->lastInsertId();
									$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
									if (is_file($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2]))
										$cat_icon = $this->setCategoryPhoto($PathFile . "icons" . DIRECTORY_SEPARATOR . $value[2], $subsubId);
									else
										$cat_icon = 0;
									if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1]))
										$thumbnail_icon = $this->setCategoryPhoto($PathFile . "banners" . DIRECTORY_SEPARATOR . $value[1], $subsubId, true);
									else
										$thumbnail_icon = 0;
									$db->query("UPDATE `engine4_album_categories` SET `cat_icon` = '" . $cat_icon . "',`thumbnail` = '" . $thumbnail_icon . "' WHERE category_id = " . $subsubId);
									$db->query("UPDATE `engine4_album_categories` set `title` = category_name WHERE category_id = ".$subsubId );
									$db->query("UPDATE `engine4_album_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'))  WHERE category_id = ".$subsubId);
									$db->query("UPDATE `engine4_album_categories` SET `order` = `category_id`  WHERE category_id = ".$subsubId);
								}
							}
							$db->query("UPDATE `engine4_album_categories` set `title` = category_name WHERE category_id = ".$subId );
							$db->query("UPDATE `engine4_album_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'))  WHERE category_id = ".$subId);
							$db->query("UPDATE `engine4_album_categories` SET `order` = `category_id`  WHERE category_id = ".$subId);
						}
					}
				$db->query("UPDATE `engine4_album_categories` set `title` = category_name WHERE category_id = ".$catId );
        $db->query("UPDATE `engine4_album_categories` set `slug` = LOWER(REPLACE(REPLACE(REPLACE(category_name,'&',''),'  ',' '),' ','-'))  WHERE category_id = ".$catId);
        $db->query("UPDATE `engine4_album_categories` SET `order` = `category_id`  WHERE category_id = ".$catId);
        }
      }
      $table_exist_albums = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
      if (!empty($table_exist_albums)) {
        $rating = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'rating\'')->fetch();
        if (empty($rating)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `rating` INT( 11 ) NOT NULL DEFAULT '0';");
        }
        $position_cover = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'position_cover\'')->fetch();
        if (empty($position_cover)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `position_cover` VARCHAR(255) NULL;");
        }
        $offtheday = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'offtheday\'')->fetch();
        if (empty($offtheday)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `offtheday` tinyint(1) NOT NULL DEFAULT '0';");
        }
        $starttime = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'starttime\'')->fetch();
        if (empty($starttime)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `starttime` date  NULL ;");
        }
        $endtime = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'endtime\'')->fetch();
        if (empty($endtime)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `endtime` date  NULL ;");
        }
        $location = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'location\'')->fetch();
        if (empty($location)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `location` TEXT  NULL ;");
        }
        $is_featured = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'is_featured\'')->fetch();
        if (empty($is_featured)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `is_featured` TINYINT( 1 ) NOT NULL DEFAULT '0';");
        }
        $is_sponsored = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'is_sponsored\'')->fetch();
        if (empty($is_sponsored)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `is_sponsored` TINYINT( 1 ) NOT NULL DEFAULT '0';");
        }
        $view_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'view_count\'')->fetch();
        if (empty($view_count)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `view_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $comment_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'comment_count\'')->fetch();
        if (empty($comment_count)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `comment_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $favourite_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'favourite_count\'')->fetch();
        if (empty($favourite_count)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `favourite_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $like_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'like_count\'')->fetch();
        if (empty($like_count)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `like_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $art_cover = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'art_cover\'')->fetch();
        if (empty($art_cover)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `art_cover` int(11) unsigned NOT NULL DEFAULT '0';");
        }
        $ip_address = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'ip_address\'')->fetch();
        if (empty($ip_address)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `ip_address` VARCHAR(45)  NULL ;");
        }
        $download_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'download_count\'')->fetch();
        if (empty($download_count)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `download_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $subcat_id = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'subcat_id\'')->fetch();
        if (empty($subcat_id)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `subcat_id` INT( 11 )  NULL DEFAULT '0';");
        }
        $subsubcat_id = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'subsubcat_id\'')->fetch();
        if (empty($subsubcat_id)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `subsubcat_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
        $category_id = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'category_id\'')->fetch();
        if (empty($category_id)) {
          $db->query("ALTER TABLE `engine4_album_albums` ADD `category_id` INT( 11 )  NULL DEFAULT 0 ;");
        }
      }
      //check 0 id category.
      $db->query("DELETE FROM `engine4_album_categories` WHERE category_id = 0 AND `category_name` = 'All Categories'");
      $table_exist_photos = $db->query('SHOW TABLES LIKE \'engine4_album_photos\'')->fetch();
      if (!empty($table_exist_photos)) {
        $rating = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'rating\'')->fetch();
        if (empty($rating)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `rating` INT( 11 ) NOT NULL DEFAULT '0';");
        }
        $offtheday = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'offtheday\'')->fetch();
        if (empty($offtheday)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `offtheday` tinyint(1) NOT NULL DEFAULT '0';");
        }
        $starttime = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'starttime\'')->fetch();
        if (empty($starttime)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `starttime` date  NULL ;");
        }
        $endtime = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'endtime\'')->fetch();
        if (empty($endtime)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `endtime` date  NULL ;");
        }
        $location = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'location\'')->fetch();
        if (empty($location)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `location` TEXT  NULL ;");
        }
        $ip_address = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'ip_address\'')->fetch();
        if (empty($ip_address)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `ip_address` VARCHAR(45)  NULL ;");
        }
        $download_count = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'download_count\'')->fetch();
        if (empty($download_count)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `download_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }

        $is_featured = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'is_featured\'')->fetch();
        if (empty($is_featured)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `is_featured` TINYINT( 1 ) NOT NULL DEFAULT '0';");
        }
        $favourite_count = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'favourite_count\'')->fetch();
        if (empty($favourite_count)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `favourite_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $is_sponsored = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'is_sponsored\'')->fetch();
        if (empty($is_sponsored)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `is_sponsored` TINYINT( 1 ) NOT NULL DEFAULT '0';");
        }
        $view_count = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'view_count\'')->fetch();
        if (empty($view_count)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `view_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $comment_count = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'comment_count\'')->fetch();
        if (empty($comment_count)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `comment_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
        $like_count = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'like_count\'')->fetch();
        if (empty($like_count)) {
          $db->query("ALTER TABLE `engine4_album_photos` ADD `like_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
        }
      }
      $db->query('DROP TABLE IF EXISTS `engine4_sesalbum_relatedalbums`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesalbum_relatedalbums` (
				`relatedalbum_id` int(11) unsigned NOT NULL auto_increment,
				`resource_id` int(11) NOT NULL,
				`album_id` INT(11) DEFAULT NULL,
				UNIQUE KEY `uniqueKey` (`resource_id`,`album_id`),
        PRIMARY KEY (`relatedalbum_id`),
				`modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      $db->query('DROP TABLE IF EXISTS `engine4_sesalbum_favourites`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesalbum_favourites` (
				`favourite_id` int(11) unsigned NOT NULL auto_increment,
				`user_id` int(11) unsigned NOT NULL,
				`resource_type` varchar(128) NOT NULL,
				`resource_id` int(11) NOT NULL,
				 PRIMARY KEY (`favourite_id`), 
				 KEY `user_id` (`user_id`,`resource_type`,`resource_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      $db->query('DROP TABLE IF EXISTS `engine4_sesalbum_ratings`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesalbum_ratings` (
			`rating_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `resource_id` int(11) NOT NULL,
      `resource_type` varchar(64) NOT NULL,
      `user_id` int(11) unsigned NOT NULL,
      `rating` tinyint(1) unsigned DEFAULT NULL, 
      UNIQUE KEY `uniqueKey` (`user_id`,`resource_type`,`resource_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      $db->query('DROP TABLE IF EXISTS `engine4_sesalbum_recentlyviewitems`;');
      $db->query('CREATE TABLE IF NOT EXISTS  `engine4_sesalbum_recentlyviewitems` (
      `recentlyviewed_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `resource_id` INT NOT NULL ,
      `resource_type` VARCHAR(64) NOT NULL DEFAULT "album",
      `owner_id` INT NOT NULL ,
      `creation_date` DATETIME NOT NULL,
      UNIQUE KEY `uniqueKey` (`resource_id`,`resource_type`, `owner_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      $db->query('DROP TABLE IF EXISTS `engine4_album_fields_maps`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_fields_maps` (
      `field_id` int(11) NOT NULL,
      `option_id` int(11) NOT NULL,
      `child_id` int(11) NOT NULL,
      `order` smallint(6) NOT NULL,
      PRIMARY KEY (`field_id`,`option_id`,`child_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      $db->query('INSERT IGNORE INTO `engine4_album_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES (0, 0, 1, 1);');
      $db->query('DROP TABLE IF EXISTS `engine4_album_fields_meta`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_fields_meta` (
      `field_id` int(11) NOT NULL AUTO_INCREMENT,
      `type` varchar(24) NOT NULL,
      `label` varchar(64) NOT NULL,
      `description` varchar(255) NOT NULL DEFAULT "",
      `alias` varchar(32) NOT NULL DEFAULT "",
      `required` tinyint(1) NOT NULL DEFAULT "0",
      `display` tinyint(1) unsigned NOT NULL,
      `publish` tinyint(1) unsigned NOT NULL DEFAULT "0",
      `search` tinyint(1) unsigned NOT NULL DEFAULT "0",
      `show` tinyint(1) unsigned DEFAULT "0",
      `order` smallint(3) unsigned NOT NULL DEFAULT "999",
      `config` text NOT NULL,
      `validators` text COLLATE utf8_unicode_ci,
      `filters` text COLLATE utf8_unicode_ci,
      `style` text COLLATE utf8_unicode_ci,
      `error` text COLLATE utf8_unicode_ci,
      PRIMARY KEY (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
      $db->query('INSERT IGNORE INTO `engine4_album_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
      (1, "profile_type", "Profile Type", "", "profile_type", 1, 0, 0, 2, 0, 999, "", NULL, NULL, NULL, NULL);');
      $db->query('DROP TABLE IF EXISTS `engine4_album_fields_options`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_fields_options` (
      `option_id` int(11) NOT NULL AUTO_INCREMENT,
      `field_id` int(11) NOT NULL,
      `label` varchar(255) NOT NULL,
      `order` smallint(6) NOT NULL DEFAULT "999",
      PRIMARY KEY (`option_id`),
      KEY `field_id` (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
      $db->query('INSERT IGNORE INTO `engine4_album_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES (1, 1, "Rock Band", 0);');
      $db->query('DROP TABLE IF EXISTS `engine4_album_fields_search`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_fields_search` (
      `item_id` int(11) NOT NULL,
      `profile_type` smallint(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`item_id`),
      KEY `profile_type` (`profile_type`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      $db->query('DROP TABLE IF EXISTS `engine4_album_fields_values`;');
      $db->query('CREATE TABLE IF NOT EXISTS `engine4_album_fields_values` (
      `item_id` int(11) NOT NULL,
      `field_id` int(11) NOT NULL,
      `index` smallint(3) NOT NULL DEFAULT "0",
      `value` text NOT NULL,
      PRIMARY KEY (`item_id`,`field_id`,`index`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
      ("album_photo_new", "sesalbum", \'{item:$subject} added {var:$count} photo(s) to the album {item:$object}:\', 1, 5, 1, 3, 1, 1),
      ("comment_album", "sesalbum", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:album}: {body:$body}\', 1, 1, 1, 1, 1, 0),
      ("comment_album_photo", "sesalbum", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:photo}: {body:$body}\', 1, 1, 1, 1, 1, 0),
			("sesalbum_albumrated", "sesalbum", \'{item:$subject} rated album {item:$object}:\', 1, 5, 1, 1, 1, 1),
			("sesalbum_photorated", "sesalbum", \'{item:$subject} rated photo {item:$object}:\', 1, 5, 1, 1, 1, 1),
			("sesalbum_favouritealbum", "sesalbum", \'{item:$subject} added album {item:$object} to favourite:\', 1, 5, 1, 1, 1, 1),
			("sesalbum_favouritephoto", "sesalbum", \'{item:$subject} added photo {item:$object} to favourite:\', 1, 5, 1, 1, 1, 1)');
      $db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
      ("sesalbum_main", "standard", "SES Advanced Photos - Album Main Navigation Menu"),
      ("sesalbum_quick", "standard", "SES Advanced Photos - Album Quick Navigation Menu");');
      $db->query('INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES ("Rebuild Album Privacy", "album_maintenance_rebuild_privacy", "sesalbum", "Sesalbum_Plugin_Job_Maintenance_RebuildPrivacy", 50);');
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
      ("core_main_sesalbum", "sesalbum", "Albums", "", \'{"route":"sesalbum_general","action":""}\', "core_main", "", 2),
      ("sesalbum_main_home", "sesalbum", "Albums Home", "", \'{"route":"sesalbum_general","action":"home"}\', "sesalbum_main", "", 1),
      ("sesalbum_main_browse", "sesalbum", "Browse Albums", "Sesalbum_Plugin_Menus::canViewAlbums", \'{"route":"sesalbum_general","action":"browse"}\', "sesalbum_main", "", 2),			
			("sesalbum_main_photohome", "sesalbum", "Photos Home", "Sesalbum_Plugin_Menus::canViewAlbums", \'{"route":"sesalbum_general","action":"photo-home"}\', "sesalbum_main", "", 3),
			("sesalbum_main_photobrowse", "sesalbum", "Browse Photos", "Sesalbum_Plugin_Menus::canViewAlbums", \'{"route":"sesalbum_general","action":"browse-photo"}\', "sesalbum_main", "", 4),
			("sesalbum_main_browsecategory", "sesalbum", "Browse Categories", "", \'{"route":"sesalbum_category"}\', "sesalbum_main","", 5),
			("sesalbum_main_manage", "sesalbum", "My Albums", "", \'{"route":"sesalbum_general","action":"manage"}\', "sesalbum_main", "", 6),
      ("sesalbum_main_upload", "sesalbum", "Add New Photos", "Sesalbum_Plugin_Menus::canCreateAlbums", \'{"route":"sesalbum_general","action":"create"}\', "sesalbum_main", "", 7),
      ("sesalbum_quick_upload", "sesalbum", "Add New Photos", "Sesalbum_Plugin_Menus::canCreateAlbums", \'{"route":"sesalbum_general","action":"create","class":"buttonlink sesalbum_icon_photos_new"}\', "sesalbum_quick", "", 1),
      ("sesalbum_admin_main_manage", "sesalbum", "Manage Albums", "", \'{"route":"admin_default","module":"sesalbum","controller":"manage"}\', "sesalbum_admin_main", "", 2),
      ("sesalbum_admin_main_photos", "sesalbum", "Manage Photos", "", \'{"route":"admin_default","module":"sesalbum","controller":"manage","action":"photos"}\', "sesalbum_admin_main", "", 3),
      ("sesalbum_admin_main_categories", "sesalbum", "Categories", "", \'{"route":"admin_default","module":"sesalbum","controller":"categories","action":"index"}\', "sesalbum_admin_main", "", 10),
			("sesalbum_admin_main_subcategories", "sesalbum", "Categories & Mapping", "", \'{"route":"admin_default","module":"sesalbum","controller":"categories","action":"index"}\', "sesalbum_admin_categories", "", 1),
      ("sesalbum_admin_main_subfields", "sesalbum", "Form Questions", "", \'{"route":"admin_default","module":"sesalbum","controller":"fields"}\', "sesalbum_admin_categories", "", 2),
      ("sesalbum_admin_main_level", "sesalbum", "Member Level Settings", "", \'{"route":"admin_default","module":"sesalbum","controller":"level"}\', "sesalbum_admin_main", "", 11),("sesalbum_admin_main_lightbox", "sesalbum", "Manage Lightbox Viewer", "", \'{"route":"admin_default","module":"sesalbum","controller":"lightbox"}\', "sesalbum_admin_main", "", 12),("sesalbum_admin_main_statistic", "sesalbum", "Statistics", "", \'{"route":"admin_default","module":"sesalbum","controller":"settings","action":"statistic"}\', "sesalbum_admin_main", "", 13);');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "auth_view" as `name`,
      5 as `value`,
      \'["everyone","registered","owner_network","owner_member_member","owner_member","owner"]\' as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "auth_comment" as `name`,
      5 as `value`,
      \'["everyone","registered","owner_network","owner_member_member","owner_member","owner"]\' as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "auth_tag" as `name`,
      5 as `value`,
      \'["everyone","registered","owner_network","owner_member_member","owner_member","owner"]\' as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "view" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "comment" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "tag" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "create" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "edit" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "delete" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("user");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "view" as `name`,
      2 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "comment" as `name`,
      2 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "tag" as `name`,
      2 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "create" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "edit" as `name`,
      2 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "delete" as `name`,
      2 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "view" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("public");');
			$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "download" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "favourite_album" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "favourite_photo" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
			$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "max_albums" as `name`,
      0 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
			$db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "imageviewer" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "rating_album" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "rating_photo" as `name`,
      1 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");');
      $db->query('INSERT IGNORE INTO `engine4_authorization_permissions`
      SELECT
      level_id as `level_id`,
      "album" as `type`,
      "tag" as `name`,
      0 as `value`,
      NULL as `params`
      FROM `engine4_authorization_levels` WHERE `type` IN("public");');
      //Move private photo to file managed path for admin.
      $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "private-photo.jpg";
      if (is_file($PathFile)) {
        if (!file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin')) {
          mkdir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin', 0777, true);
        }
        copy($PathFile, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin/private-photo.jpg');
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.private.photo', 'public/admin/private-photo.jpg');
      }
      include_once APPLICATION_PATH . "/application/modules/Sesalbum/controllers/defaultsettings.php";
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.pluginactivated', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.licensekey', $_POST['sesalbum_licensekey']);
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.checkalbum', 1);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.lightboxche', 1);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.albumche', 1);
  } else {
    $error = $this->view->translate('Please enter correct License key for this product.');
    $error = Zend_Registry::get('Zend_Translate')->_($error);
    $form->getDecorator('errors')->setOption('escape', false);
    $form->addError($error);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.checkalbum', 0);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.lightboxche', 0);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.albumche', 0);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesalbum.licensekey', $_POST['sesalbum_licensekey']);
    return;
  }
}