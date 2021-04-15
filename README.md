# Online Editor

Online editor is a free and easy to use editor written in php/js that can help you a lot to manage your websites.

It's time consuming to launch an ftp manager, to connect to your site, to download a file, to modify it and to send it back when you want to make a very small modification.\
Let's say I have the site https://site.com/ \
If I install Online Editor on it, I just have to go to this address https://site.com/editor/, enter my password and I instantly have access to the files of my site and I can modify them.\
It's also working on mobile !

It's easy and secure, try Online Editor !

![editor image](https://cdn.discordapp.com/attachments/750339759716565092/832027164895346708/editor1.png)

![editor image](https://cdn.discordapp.com/attachments/750339759716565092/832026451825131540/editor2.png)

![Editor image on mobile](https://cdn.discordapp.com/attachments/750339759716565092/832141522441994280/Screenshot_20210415-083224_Chrome.jpg)

![Editor image on mobile](https://cdn.discordapp.com/attachments/750339759716565092/832141522899697704/Screenshot_20210415-083303_Chrome.jpg)


### features

Online Editor includes dozens of features.
- view and navigate folders and files like a normal file explorer
- create/edit/delete/move/rename/duplicate/download/upload files and folders
- advanced and fully customizable syntax highlighting
- merge management, to edit a file with several people at the same time
- code autocompletion
- input suggestion
- indentation management
- customizable shortcuts to navigate very quickly in folders
- dark theme/light theme/custom theme
- change default directory
- show or hide hidden files and folders
- shortcuts like Ctrl+S to edit a file quickly

## Installation

**__WARNING__** Don't clone all the repositorie, it will not working.\
You have to download install.php, put it at the root of your website and run it by opening the address corresponding to your site https://YOUR_SITE.COM/install.php\
To download install.php, you can use wget command.\
move to the root of your site with `cd` and type:
```
wget https://raw.githubusercontent.com/mactul/online_editor/install.php
```

**__WARNING__** between the moment you put install.php online and the moment you launch it and enter the password in the interface, your site is vulnerable because someone could enter a password in your place !


## Useful shortcuts

- `Ctrl+S` in file edition to save the file without closing it.
- `Ctrl+Q` in file edition to save and close the file.
- `F5` or `F9` (do the same) open the file on a new tab. useful to web files, to test the code.


## Customisation:

Online Editor is fully configurable.\
At the bottom left, you have a setting wheel, which allows you to make some initial adjustments.\
You can change the default folder, the theme to dark or light and you can show or hide hidden folders and files.

If you want to make finer adjustments, the bulk of what is customisable is in the editor/settings/ folder.\
If you want to change the colours of the dark theme for example, edit the colors.css file.\
You just have to replace the hexadecimal values of the colours by the ones you want.\
To reset the changes, run install.php again, which will ask you for your current password and a new one if you want to change it.\

This is not user friendly at all, but if you want to change keyboard shortcuts such as Ctrl+S, Ctrl+Q, etc... You can modify the very end of the editor/editor.php file, there is a very short js script that handles this.