<?php

namespace LuceneSearch\Plugin;

use LuceneSearch\Model\Configuration;

class Install {

    public function installConfigFile()
    {
        $configFile = \Pimcore\Config::locateConfigFile('lucenesearch_configurations');

        if (is_file($configFile . '.BACKUP'))
        {
            rename($configFile . '.BACKUP', $configFile . '.php');
            return TRUE;
        }

        Configuration::set('frontend.index', 'website/var/search/frontend/index/');

        Configuration::set('frontend.ignoreLanguage', FALSE);
        Configuration::set('frontend.ignoreCountry', TRUE);
        Configuration::set('frontend.ignoreRestriction', TRUE);

        Configuration::set('frontend.restriction.class', '');
        Configuration::set('frontend.restriction.method', '');

        Configuration::set('frontend.auth.useAuth', FALSE);
        Configuration::set('frontend.auth.username', '');
        Configuration::set('frontend.auth.password', '');

        Configuration::set('frontend.fuzzySearch', FALSE);
        Configuration::set('frontend.enabled', FALSE);
        Configuration::set('frontend.urls', array());
        Configuration::set('frontend.validLinkRegexes', array());
        Configuration::set('frontend.invalidLinkRegexesEditable', array());
        Configuration::set('frontend.invalidLinkRegexes', '@.*\.(js|JS|gif|GIF|jpg|JPG|png|PNG|ico|ICO|eps|jpeg|JPEG|bmp|BMP|css|CSS|sit|wmf|zip|ppt|mpg|xls|gz|rpm|tgz|mov|MOV|exe|mp3|MP3|kmz|gpx|kml|swf|SWF)$@');
        Configuration::set('frontend.categories', array());
        Configuration::set('frontend.allowedSchemes', array('http'));
        Configuration::set('frontend.ownHostOnly', FALSE);
        Configuration::set('frontend.crawler.maxLinkDepth', 15);
        Configuration::set('frontend.crawler.maxDownloadLimit', 0);
        Configuration::set('frontend.crawler.contentStartIndicator', '');
        Configuration::set('frontend.crawler.contentEndIndicator', '');
        Configuration::set('frontend.sitemap.render', FALSE);

        Configuration::setCoreSettings(

            array(
                'forceStart' => FALSE,
                'forceStop' => FALSE,
                'running' => FALSE,
                'started' => FALSE,
                'finished' => FALSE
            )
        );

        return TRUE;
    }

    public function createDirectories()
    {
        //create folder for search in website
        if( !is_dir( (PIMCORE_WEBSITE_PATH . '/var/search' ) ) )
        {
            mkdir(PIMCORE_WEBSITE_PATH . '/var/search', 0755, true);
        }

        $index = PIMCORE_DOCUMENT_ROOT . '/' . Configuration::get('frontend.index');

        if (!empty($index) and !is_dir($index))
        {
            mkdir($index, 0755, true);
            chmod($index, 0755);
        }

        return TRUE;

    }

    public function createRedirect()
    {
        $redirects = new \Pimcore\Model\Redirect\Listing();

        $redirects->setCondition('source = ?', '/\/sitemap.xml/');
        $redirects->load();

        foreach ($redirects->getRedirects() as $redirect) {
            $redirect->delete();
        }

        //add redirect for sitemap.xml
        $redirect = new \Pimcore\Model\Redirect();
        $redirect->setValues(array('source' => '/\/sitemap.xml/', 'target' => '/plugin/LuceneSearch/frontend/sitemap', 'statusCode' => 301, 'priority' => 10));
        $redirect->save();

        return TRUE;
    }

    public function removeConfig()
    {
        $configFile = \Pimcore\Config::locateConfigFile('lucenesearch_configurations');

        if (is_file( $configFile . '.php' ))
        {
            rename($configFile, $configFile  . '.BACKUP');
        }
    }

}