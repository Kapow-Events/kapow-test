How to deploy to production
===========================

mv .git.hidden .git

vi public_html/.git/config

change the username in url line to your github username

url = https://username@github.com/colorjar/events-wired.git

save file

merge code from develop branch into master and push to github

git pull origin master (run this on the production box to pull down changes from github)

mv .git .git.hidden
