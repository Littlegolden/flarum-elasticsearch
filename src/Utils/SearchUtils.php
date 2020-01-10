<?php

namespace Plugin\ESearch\Utils;

use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;


class SearchUtils
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var string $settingsPrefix
     */
    public $settingsPrefix = 'alongwy-es.';

    /**
     * LoadSettingsFromDatabase constructor
     *
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $hosts = [
            [
                'host' => $this->settings->get($this->settingsPrefix . 'host', "elasticsearch"),
                'port' => $this->settings->get($this->settingsPrefix . 'port', 9200),
                'scheme' => $this->settings->get($this->settingsPrefix . 'scheme', "http")
            ],
        ];
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    function getESearch(): Client
    {
        return $this->client;
    }

    // 构造文档
    function buildESDocument(Discussion $discussion, Post $post, $count)
    {
        $data = [
            'index' => 'flarum',
            'type' => 'post',
            'id' => $post->id,
            'body' => [
                "discId" => $discussion->id,
                "title" => $discussion->title,
                "content" => $post->content,
                "time" => strtotime($post->created_at),
                "discTime" => strtotime($post->discussion->created_at),
                "count" => $count
            ]
        ];
        return $data;
    }
}