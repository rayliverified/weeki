<div class="container">

### “Instant Messenger” documentation by “SoftDev”

* * *

# “Instant Messenger”

<div class="borderTop">

<div class="span-6 colborder info prepend-1">

**Created: 08/20/2016  
By: SoftDev  
Email: [Technoengineering@hotmail.co.uk](mailto:Technoengineering@hotmail.co.uk)**

</div>

<div class="span-12 last">

Fast, fully designed and instant messaging app for android with a lot of great features. With this application, you can talk with anyone in your friend list without any limitations with attachment feature like sharing images and sending emoticons. The app is fully designed and is purely coded to make it easier to understand for developers. You can find friends, add them and start talking with them without any wait just like instant messaging.

</div>

</div>

* * *

## Table of Contents

1.  [What is this?](#about)
2.  [Features](#features)
3.  [App Configuration](#config)
4.  [Google Cloud Messaging (GCM) Setup](#gcmsetup)
5.  [MySQL Queries](#mysql)
6.  [Frequently Asked Questions _(FAQ)_](#faq)
7.  [Licenses](#licenses)

* * *

### **A) What is this?** - [top](#toc)

Weeki Messenger, weeki is just a random name that I gave to this application. It is actual an instant messaging app just like WhatsApp for android platform and it has alot of features which includes group messaging, image sharing, emoticons, profile, friend system and alot more. It's really easy to re-skin and re-developed in every way you want. You can add alot more features without any worries of errors. The code is very well written to make it easier for every developer to understand (like you).

* * *

### **B) Features** - [top](#toc)

1.  Personal Messages
2.  Group Messages
3.  Image Sharing
4.  Emoticons
5.  Client-sided Friends system
6.  Push Notifications
7.  Fully designed
8.  Encrypted Passwords
9.  Profile View

* * *

### **C) App Configuration** - [top](#toc)

<div>You've to configure some app and web server settings before making your app run. </div>

<div>Open upload.php from **'/include/'** folder and replace **'your-host-ip'** with your host ip address. This file is used to upload images.</div>

<div>Second, open Config.php from **'/include/'** folder and replace database credentials with your database credentials and configure name and host with your database name and web-server host.</div>

<div>Third, goto Config.java file of our project which is located </div>

<div>under **'\app\src\main\java\com\softdev\weekimessenger\Configuration\'** using android studio or any ordinary text editor. Now change your </div>

<div>BASE_URI with your web server address and replace **'your-ip'** with your host ip address.</div>

<div>It's all done!</div>

* * *

* * *

### **C) GCM Setup** - [top](#toc)

<div>You've to first setup GCM (Google cloud messaging) to make your app run properly. </div>

<div>Follow these steps to obtain your API key:</div>

<div>• Goto [Google Developer Console](https://console.developers.google.com/project) and create your new app. Set a name of your new app (i.e: 'messenger') and </div>

<div>click Dashboard on the left side.</div>

<div>• Now click **'Library'** from the left side and select 'Google CLoud Messaging' from Mobile APIs category and press Enable from top behind search bar.</div>

<div>• Now you've successfully enabled GCM for your app but you still need credentials to start working with it.</div>

<div>Click 'Credentials' from the left side and press **'Create credentials'**. Choose API Key from the drop down menu and click 'Server key'. </div>

<div>You'll now receive api key which is use to operate GCM. Copy it and paste it on PHP (config.php), replace **'your-gcm-id'** with your new API Key. </div>

<div>Config file is located under **'/include/'** folder.</div>

<div>- Now you've to integrate GCM in your android project too, so for that, follow these steps:</div>

<div>You need GCM configuration file to operate GCM in your project. Go [here](https://developers.google.com/cloud-messaging/android/client) and </div>

<div>click 'Get a Configuration file' which will redirect you to Developer console where you'll be </div>

<div>asked for App name and package name. Write in 'weeki-messenger' as App name and 'com.softdev.weekimessenger' as package name. </div>

<div>You can change app name and project name from android studio if you want your own.</div>

<div>• Now click **'Choose and configure services'** and press 'Enable google cloud messaging' which will give you another button as 'Download google-services.json' </div>

<div>which will download configuration file on your PC. After downloading **'google-services.json'** file, move it to project app folder and you're all done.</div>

<div>(If you're still having some trouble with this, feel free to contact me).</div>

* * *

* * *

### **E) MySQL Queries** - [top](#toc)

You've to first setup the database before running the app because it is the main priority of our application. I've created the database very smooth and easy to use and it'll surely make our messaging system fast. Execute these queries on MySQL with any IDE or web tool and it'll automatically create tables on your specified database.

<pre>	CREATE TABLE weeki.users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  registration_id TEXT DEFAULT NULL,
  api TEXT NOT NULL,
  created_At DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  disabled TINYINT(1) DEFAULT 0,
  status VARCHAR(130) DEFAULT 'Just another user',
  icon TEXT DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX id (id)
);

CREATE TABLE weeki.groups (
  group_id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  icon TEXT DEFAULT NULL,
  description VARCHAR(130) NOT NULL,
  creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (group_id)
);

CREATE TABLE weeki.messages (
  message_id INT(11) NOT NULL AUTO_INCREMENT,
  receiver_id INT(11) NOT NULL,
  sender_id INT(11) NOT NULL,
  msg_type INT(11) NOT NULL DEFAULT 0,
  message VARCHAR(255) NOT NULL,
  created_At DATETIME NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (message_id),
  UNIQUE INDEX id (message_id),
  CONSTRAINT FK_messages_users_id FOREIGN KEY (receiver_id)
    REFERENCES weeki.users(id) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE TABLE weeki.messages_receipt (
  message_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  is_delivered INT(11) NOT NULL DEFAULT 0,
  CONSTRAINT FK_messages_receipt_messages_id FOREIGN KEY (message_id)
    REFERENCES weeki.messages(message_id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT FK_messages_receipt_users_id FOREIGN KEY (user_id)
    REFERENCES weeki.users(id) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE TABLE weeki.group_messages (
  message_id INT(11) NOT NULL AUTO_INCREMENT,
  group_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  msg_type INT(11) NOT NULL DEFAULT 0,
  message VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL,
  PRIMARY KEY (message_id),
  CONSTRAINT FK_group_messages_groups_group_id FOREIGN KEY (group_id)
    REFERENCES weeki.groups(group_id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT FK_group_messages_users_id FOREIGN KEY (user_id)
    REFERENCES weeki.users(id) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE TABLE weeki.group_members (
  group_id INT(11) DEFAULT NULL,
  user_id INT(11) DEFAULT NULL,
  CONSTRAINT FK_group_members_groups_group_id FOREIGN KEY (group_id)
    REFERENCES weeki.groups(group_id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT FK_group_members_users_id FOREIGN KEY (user_id)
    REFERENCES weeki.users(id) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE TABLE weeki.group_receipt (
  message_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  is_delivered INT(11) NOT NULL DEFAULT 0,
  INDEX FK_group_stats_groups_group_id (user_id),
  CONSTRAINT FK_group_stats_group_messages_message_id FOREIGN KEY (message_id)
    REFERENCES weeki.group_messages(message_id) ON DELETE RESTRICT ON UPDATE RESTRICT
);

CREATE FUNCTION weeki.CreateGroup(GroupName VARCHAR(50), GroupIcon TEXT, GroupDescription VARCHAR(130), GroupCreator INT)
  RETURNS int(11)
  DETERMINISTIC
BEGIN
  DECLARE groupID INT;
  INSERT INTO groups (name, icon, description) VALUES (GroupName, GroupIcon, GroupDescription);
  SET groupID = LAST_INSERT_ID();
  INSERT INTO group_members VALUES (groupID, GroupCreator);
  RETURN groupID;
END;

CREATE FUNCTION weeki.AddMessage(r VARCHAR(255), s INT, t INT, m VARCHAR(255), creation DATETIME)
  RETURNS int(11)
  DETERMINISTIC
BEGIN
        DECLARE lastID INT;
        DECLARE receiver INT;
        SELECT id INTO receiver from users WHERE username=r;
        INSERT INTO messages (receiver_id, sender_id, msg_type, message, created_At) values(receiver, s, t, m, creation);
        SET lastID = LAST_INSERT_ID();
        INSERT INTO messages_receipt (message_id, user_id, is_delivered) VALUES (lastID, receiver, 0);
        RETURN lastID;
END;

CREATE FUNCTION weeki.AddGroupMessage(gid INT, s INT, t INT, m VARCHAR(255), creation DATETIME)
  RETURNS int(11)
  DETERMINISTIC
BEGIN
  DECLARE lastID INT; DECLARE rowCount INT;
  SELECT COUNT(*) INTO rowCount FROM group_members WHERE user_id = s AND group_id = gid;
  IF rowCount = 1 THEN
  INSERT INTO group_messages (group_id, user_id, msg_type, message, created_at) VALUES (gid, s, t, m, creation);
  SET lastID = LAST_INSERT_ID();
  INSERT INTO group_receipt (message_id, user_id, is_delivered) SELECT gm.message_id, gmembers.user_id, 0 FROM
  group_messages gm LEFT JOIN group_members gmembers ON gmembers.group_id = gm.group_id WHERE gm.group_id = gid AND gm.message_id = lastID AND NOT gmembers.user_id = s;
  RETURN lastID;
  ELSE
    return 0;
  END IF;
END;

CREATE DEFINER = 'root'@'localhost'
FUNCTION weeki.AddGroupMember(GroupID INT, MemberName VARCHAR(255), Username INT)
  RETURNS int(11)
BEGIN
  INSERT INTO group_members VALUES (GroupID, MemberName);
  RETURN 1;
END;
		</pre>

* * *

### **F) Frequently Asked Questions (FAQ)** - [top](#toc)

<div>**Q) Is this an instant messaging application?**</div>

<div>A) Yes.</div>

<div>**Q) Does it support group messaging and personal messaging together?**</div>

<div>A) Yes.</div>

<div>**Q) Does it support emoticons?**</div>

<div>A) Yes.</div>

<div>**Q) Does it support attachments (like images)?**</div>

<div>A) Yes.</div>

<div>**Q) Can I add more attachments (like voice, video) easily?**</div>

<div>A) Yes you can. I've made everything simple and easier to understand and </div>

<div>you can easily add voice and video attachment support by adding a couple </div>

<div>of codes on PHP and android project. You can also contact me using email and I can help you out.</div>

<div>**Q) Can I ask for bug fixes and other support needed to run this app?**</div>

<div>A) Yes you can. Feel free to contact me on my personal email and I'll catch you up as soon as possible for me.</div>

<div>**Q) How does this app syncs the messages from server?**</div>

<div>A) It uses GCM to get notifications. Whenever there's a new message our server sends </div>

<div>a push notification and notify our app about a new message. All messages are stored in server no matter if your app is online or offline.</div>

<div>**Q) Does this app sync messages which were send when my app was offline?**</div>

<div>A) Yes, ofcourse. As written above, all the messages are stored on server and marked as delivered whenever your app gets it. </div>

<div>If somehow your app is offline, server will save the message on database and will wait for the app to get online to send the message(s).</div>

<div>**Q) Do I need a very fast server to run this app smooth?**</div>

<div>A) Not really. You just need a web host with decent specifications to run your app smoothly and fast.</div>

<div>**Q) How many users are limited for group messaging?**</div>

<div>A) None. You can add/kick any and how much members you want. There's no limitation as long as your server is having pretty decent specifications. Otherwise, it's all good.</div>

<div>**Q) How do I setup GCM?**</div>

<div>A) I've created a help document in this same folder, with a name of 'gcm-setup'. You'll find every important detail to setup GCM.</div>

<div>**Feel free to ask more questions on my email (if you've any which is not written above).**</div>

* * *

### **G) Licenses** - [top](#toc)

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Application Icon, and other icons inside of it is licensed under [CC Attribution-Noncommercial-No Derivate 4.0](http://creativecommons.org/licenses/by-nc-nd/4.0/).</span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">The Slim Framework is licensed under the MIT license. See [License File](https://github.com/slimphp/Slim/blob/3.x/LICENSE.md)<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;"> for more information.</span></span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;"><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Rockerhieu Emojicon is licensed under [Apache Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html).</span></span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;"><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Hdodenhof CircularImageView </span></span><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">is licensed under </span>[Apache Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">.</span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Square Picasso </span><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">is licensed under </span>[Apache Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">.</span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Android Volley </span><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">is licensed under </span>[Apache Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">.</span>

<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">Google GCM </span><span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">is licensed under </span>[Apache Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)<span style="color: rgb(51, 51, 51); font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; font-size: 16px; line-height: 24px;">.</span>

[Go To Table of Contents](#toc)

* * *

</div>