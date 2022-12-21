# IBK sync stub

## Installation

- Clone Repo into desired location
- Change directory to new location `cd /my/repo/directory`
- run `composer install` as non-root user
- Edit config.php and fill in all necessary variables
    - Infusionsoft Creds are obtained [here](https://keys.developer.keap.com/my-apps)
- Update token.php file permissions
    - `chmod g+w token.php`
    - `chown :www-data token.php`
- Create a vhost record for the site and updated the base url variable in config.php
- Authorize Infusionsoft by visiting your.url.com/authorize.php?state={{IFS_APP_NAME}}
- Setup cronjob as sudo user for refresh_tokens.php. This will run on the 0, 6, 12, and 18 hr of each day. Can be run
  via CLI as well.
    - `0 0,6,12,18 * * * php /path/to/refresh_tokens.php 2>> /path/to/error.log;`

## Configuration

- For debugging, you can `define("DEBUG", true);`
