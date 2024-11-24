# sitemap2indexnow 
## A submitter for IndexNow which identifies updated URLs by examining sitemaps

sitemap2indexnow is a PHP script which discovers updated pages on your website by examining your website's xml sitemap(s) and submitting the recently modified URLs to IndexNow.

This is useful if, for example, your website CMS does not have an IndexNow submitter module but your website does have an autoupdating xml sitemap which can be used as a source of data to identify recently updated URLs.

[IndexNow](https://www.indexnow.org/) is supported by several search engines including Bing, and supports rapid search-engine indexing of new and changed files on your website.

You can configure it to work with a single sitemap or with multiple sitemaps, and with either xml sitemaps or sitemap indexes or a combination of both.

This single file script is intended to be installed in cron and scheduled to be run at regular intervals. On each run it looks for URLs updated since the previous run and submits them to IndexNow. It can submit up to 10,000 URLs in a single run, or just one or two, depending on how many updated pages it finds. If there are no recently updated pages it will do nothing and then recheck again after the specified interval.

It has been written with a Linux environment in mind (specifically Ubuntu) but should also be able to be used on other Linux distributions and on Windows PHP

## Brief installation instructions

1. Place file sitemap2indexnow.php in any suitable directory, e.g. /home/anyuser/bin/, and make it executable.
2. Attend to all the setup requirements listed at the top of the file
3. Configure a cron job (or other scheduler) to run the script at regular intervals

## Detailed installation instructions

Place the file sitemap2indexnow.php in any suitable directory, e.g. /home/anyuser/bin/, and make it executable.

Attend to all the following setup requirements, which are listed at the top of the file.

### 1. Satisfy prerequisites

 - A scheduler (usually cron) is required
 - A current version of php with php-curl. Install if not already installed e.g. sudo apt install php-curl
 - Your website must have autoupdating xml sitemap(s)

### 2. Determine the path to your PHP cli binary

e.g. in terminal: which php

Check the shebang at the top of the sitemap2indexnow script and if necessary edit it to match the location of your php binary

### 3. Complete the $domain line by specifying the domain of your website. 
e.g. $domain ='yourdomain.com';

### 4. Edit the $sitemapFiles line to add your xml sitemap url(s). 
You can enter multiple sitemap files. They can be either sitemap files, or sitemapindex files, or a mixture of both. Format the entries as per the examples near the top of the script.

### 5. You need to enter your IndexNow api key. 
You can obtain it from [Bing IndexNow](https://www.bing.com/indexnow/getstarted). Enter it as the value for $key, and follow the instructions to enter it in a file on your website

### 6. Enter the location of the api key on your website
This is usually "https://yourdomain.com/$key.txt"

### 7. Set the target site for the upload of updated URLs
This is usually "https://www.bing.com/indexnow"

### 8. Set the interval between uploads, in hours
e.g. 6

### 9. Configure your cron job or other scheduler to run your sitemap2indexnow.php script at regular intervals. 
IMPORTANT: the interval set in the scheduler must match the value set in the step above. If they are not set to the same value then there may be duplicate uploads, or missing uploads, of updated URLs.

Remember to include the full path to the script when creating a cron job, i.e.

Not:

sitemap2indexnow.php

But:

/full/path/to/sitemap2indexnow.php

### 10. Test it.
Edit some pages and save them. Check your xml sitemap shows the lastmod datetime as updated to time of editing.

Run sitemap2indexnow.php manually so that you don't have to wait for the cron job to trigger, and so that you can see the output:

/path/to/sitemap2indexnow.php

If no errors are reported then check on your Bing Webmaster tools account to see if your edited pages are showing in the IndexNow section of the webmaster tools. My experience is that on first use, soon after you api key has been first used, it may appear not to have worked. But once the connection is established the latest batch of uploads is noted almost immediately, usually listing only one of the inluded URLs but with a timestamp matching your run of the script.

#### Debugging

On a successful submission Bing does not give a success message, just the absence of an error message.

Errors are most likely to be caused either by a script configuration error (typos in api key, domain name, etc) or in sitemap configuration.

Turn on debugging (item 10 in the setup parameters) as this may reveal where the problem lies, e.g. no sitemaps found in a sitemap index, no URLs found in a sitemap, etc

Recheck the configuration parameters at the top of the file, checking that they are entered in valid php format, using double-quotes where specified.

Recheck that you have installed the api key on your webserver as per IndexNow instructions, and specified that correctly in the script configuration for both $key and $keyLocation.

Check that you have a standard format xml sitemap on your site and correctly configured in the script. Note that the sitemap configuration is in an array, so the sitemap URL(s) must be enclosed in quotes, separated by commas if you have multiple sitemaps, and with the whole string enclosed in []


## Changelog

### 1.0

* First version.




