# ThirdSetMauticTimingBundle

## [Description](id:description)
The ThirdSetMauticTimingBundle is a [Mautic](http://www.mautic.org) plugin that allows you to set a cron timing expression on any Campaign action events (such as a "Send Email" action).

### Purpose
For example, you could add a cron expression that only allows sending on weekdays during working hours in New York.

### Compatibility
 * This plugin has been tested with up to v2.15.1 of Mautic.
 * This plugin has been tested with up to v7.1.30 of PHP.

### Features
 * Adds the full ability of cron syntax to your campaign actions allowing you to only send emails at certain times.
 * Use different cron expressions on different campaign actions.
 * Enter a timezone to use when evaluating the cron expression (this allows you to send an email during working hours in your own timezone, for example).
 * Use different time zones for different cron expressions/campaign actions.
 * Use the Contact's time zone (if available) to evaluate the cron expression. It will fall back to use any time zone you specify (or the System Default time zone) if the Contact's time zone isn't known.


## [Installation](id:installation)
1. Download or clone this bundle into your Mautic `/plugins` folder.
2. Manually delete your cache (app/cache/prod).
3. In the Mautic GUI, go to the gear and then to Plugins.
4. Click the down arrow in the top right and select "Install/Upgrade Plugins". 
   Note: newer versions of Mautic just have an "Install/Upgrade Plugins" button
  (without the dropdown arrow).
5. You should now see the Timing plugin in your list of plugins.
6. Run the following console commands to update the database:

```
php app/console doctrine:schema:update --dump-sql
php app/console doctrine:schema:update --force
```

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

If a time zone is specified in this field, the cron expression will evaluate against the selected time zone.  This allows you to do things such as only sending emails during your working hours.

If you've enabled the "Use Contact's Time Zone" field, the contact's time zone would be used instead of this field (unless the Contact's time zone is unknown in which case the plugin will fall back to the time zone specified here).
  
## Testing the Plugin

This plugin includes a suite of unit tests. You will need to have PHPUnit installed in order to run the tests.

#### Installing PHPUnit on AlpineLinux

```
apk update && apk add ca-certificates && update-ca-certificates && apk add openssl
/usr/bin/wget https://phar.phpunit.de/phpunit.phar -O /usr/bin/phpunit && chmod 755 /usr/bin/phpunit
```

#### Running the Tests

Put the plugin dir in mautic plugins dir (`./plugins`) and run in `mautic` root dir:

```
bin/phpunit --bootstrap vendor/autoload.php --configuration app/phpunit.xml.dist 'MauticPlugin\ThirdSetMauticTimingBundle\Tests\Helper\TimingHelperTest' plugins/ThirdSetMauticTimingBundle/Tests/Helper/TimingHelperTest.php 
```

## Credits

This plugin is developed and maintained by [Third Set Productions](http://www.thirdset.com) the makers of [AdPlugg](http://www.adplugg.com).

## Disclaimer

This plugin is licensed under GPLv3. 

The GPL clearly explains that there is no warranty for this free software. Please see the included license.txt file for details.
