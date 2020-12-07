# ThirdSetMauticTimingBundle

## [Description](id:description)

The ThirdSetMauticTimingBundle is a [Mautic](http://www.mautic.org) plugin that allows you to set a cron timing expression on any Campaign action event (such as a "Send Email" action).

### Purpose

For example, you could add a cron expression that only allows sending on weekdays during working hours in New York.

### Compatibility

This plugin works on both Mautic 2.x and Mautic 3.x and should work on most versions of PHP.

#### Mautic 3.x

This plugin has been tested with up to **Mautic v3.2.1**.

#### Mautic 2.x

This plugin has been tested with up to **Mautic v2.16.0**.

#### PHP

This plugin has been tested with up to **PHP v7.3.19**.

### Features

* Adds the full ability of cron syntax to your campaign actions allowing you to only send emails at certain times.
* Use different cron expressions on different campaign actions.
* Enter a timezone to use when evaluating the cron expression (this allows you to send an email during working hours in your own timezone, for example).
* Use different time zones for different cron expressions/campaign actions.
* Use the Contact's time zone (if available) to evaluate the cron expression. It will fall back to use any time zone you specify (or the System Default time zone) if the Contact's time zone isn't known.

## [Installation](id:installation)

1. Download or clone this bundle into your Mautic `/plugins` folder.
2. Manually delete your cache (`var/cache/prod` for Mautic 3.x and `app/cache/prod` for Mautic 2.x).
3. In the Mautic GUI, go to the :gear: icon in the top right and then to Plugins.
4. Click the "Install/Upgrade Plugins" button in the top right. Note: if you are on an older version of Mautic, click the drowpdown arrow in the top right and then choose "Install/Upgrade Plugins".
5. You should now see the Timing plugin in your list of plugins.
6. Run the following console commands to update the database:

```bash
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```

_Note: For Mautic 2.x, replace `php bin/console` with `php app/console` in the above commands._

## [Usage](id:usage)

### Timing

After installing the plugin, you will now see a new Timing section when adding or editing Campaign actions.

#### Expression Field

The expression field allows you to enter a cron expression for the timing to be evaluated against. See the [Wikipedia Cron Page](https://en.wikipedia.org/wiki/Cron) for a good overview of cron syntax.

Note that it is best to use ranges (ex: "1-5") instead of exact values. This is because the expression is evaluated when the campaign is triggered and when that happens is external to this plugin.

Example:

`* 09-17 * * 1-5` (Weekdays, 9AM to 5PM)

#### Use Contact's Time Zone Field

When this field is enabled, the plugin will evaluate the cron expression off of the Contact's time zone.  This allows you to only send emails during the Contact's working hours.

The contact time zone is evaluated in the following order:

1. The timezone field of the contact (e.g. `mautic.lead.field.timezone`) (Mautic > 2.6.1), else:
2. The timezone of the last ip address used by the contact, else:
3. The value of the `Time Zone Field` in the action, else:
4. The system default time zone

#### Time Zone Field

If a time zone is specified in this field, the cron expression will evaluate against the selected time zone.  This allows you to do things such as only sending emails during _your_ working hours.

If you've enabled the "Use Contact's Time Zone" field, the contact's time zone would be used instead of this field (unless the Contact's time zone is unknown in which case the plugin will fall back to the time zone specified here).

## Testing the Plugin

This plugin includes a suite of unit tests. You will need to have PHPUnit installed in order to run the tests.

### Installing PHPUnit on AlpineLinux

```bash
apk update && apk add ca-certificates && update-ca-certificates && apk add openssl
/usr/bin/wget https://phar.phpunit.de/phpunit.phar -O /usr/bin/phpunit && chmod 755 /usr/bin/phpunit
```

### Installing PHPUnit on Debian Linux

```bash
curl https://phar.phpunit.de/phpunit.phar -L -o /usr/bin/phpunit && chmod 755 /usr/bin/phpunit
```

### Running the Tests

Change directories into the plugin dir (`cd plugins/ThirdSetMauticTimingBundle`) and then run:

```bash
phpunit
```

## Credits

This plugin is developed and maintained by [Third Set Productions](http://www.thirdset.com) the makers of [AdPlugg](http://www.adplugg.com).

## Disclaimer

This plugin is licensed under GPLv3.

The GPL clearly explains that there is no warranty for this free software. Please see the included license.txt file for details.
