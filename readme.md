How to deploy to production
===========================

mv .git.hidden .git

vi public_html/.git/config

change the url line to

url = https://<username>@github.com/colorjar/events-wired.git

save file

merge code from develop branch into master

git pull origin master


mv .git .git.hidden
