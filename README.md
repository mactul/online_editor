# Online Editor

Online editor is a free and easy to use editor written in php/js that can help you a lot to manage your websites.

it is time consuming to launch an ftp manager, to connect to your site, to download a file, to modify it and to send it back when you want to make a very small modification.\
Let's say I have the site https://site.com/.\
If I install Online Editor on it, I just have to go to this address https://site.com/editor/, enter my password and instantly I have access to the files of my site and I can modify them.\
It's easy and secure !

![editor image](https://cdn.discordapp.com/attachments/750339759716565092/832027164895346708/editor1.png)

![editor image](https://cdn.discordapp.com/attachments/750339759716565092/832026451825131540/editor2.png)

### features

Online Editor includes dozens of features.\
- view and navigate folders and files like a normal file explorer\
- create/edit/delete/move/rename/duplicate/download/upload files and folders\
- advanced and fully customizable syntax highlighting\
- merge management, to edit a file with several people at the same time\
- code autocompletion\
- input suggestion\
- indentation management\
- customizable shortcuts to navigate very quickly in folders\
- dark theme/light theme/custom theme\
- change default directory\
- show or hide hidden files and folders\
- shortcuts like Ctrl+S to edit a file quickly\

## Installation

**__WARNING__** Don't clone all the repositorie, it will not working.\
You have to download install.php, put it at the root of your website and run it by opening the address corresponding to your site https://YOUR_SITE.COM/install.php
To download install.php, you can use wget command
move to the root of your site with `cd` and type:
```
wget https://raw.githubusercontent.com/mactul/online_editor/install.php
```

**__WARNING__** between the moment you put install.php online and the moment you launch it and enter the password in the interface, your site is vulnerable because someone could enter a password in your place !