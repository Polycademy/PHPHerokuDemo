# PHPHerokuDemo

Minimal demo of deploying a PHP MySQL-based Heroku Application.

## Deployment ##

You need: `git`, `composer`, `heroku`. The below script sets up a new Heroku application from scratch assuming you have a Heroku account.

```sh
app_name='X-php-demo' # fill in your own name

git clone https://github.com/Polycademy/PHPHerokuDemo.git

cd PHPHerokuDemo

heroku login

heroku apps:create "$app_name"

heroku addons:create cleardb:ignite --app "$app_name"

heroku ps:scale web=1 --app "$app_name"

composer update

git commit --all --allow-empty --message='Trigger Deployment'

# if the remotes were not successfully added, try:
# git remote add heroku "https://git.heroku.com/${app_name}.git" || true
git push heroku master

heroku open
# or use curl
curl "https://${app_name}.herokuapp.com/"
```