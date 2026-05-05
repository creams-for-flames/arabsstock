<?php

namespace App\Jobs;

use App\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Elasticsearch\ClientBuilder;
use App\Models\Image;
use App\Models\Video;
use App\Models\Vector;
use Illuminate\Support\Facades\Log;

class PrepareElasticsearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        Log::channel('info')->info("Start PrepareElasticsearch ");
        $this->index_images();
        $this->index_videos();
        $this->index_vectors();

        Log::channel('info')->info("End PrepareElasticsearch Vectors");


    }

    private function elastic_last_item($client, $index)
    {
        try {
            $response = $client->search([
                'index' => $index,
                'body' => [
                    'sort' => [
                        "search_result_data.id" => ["order" => "desc"],
                    ],
                    'size' => 1
                ]
            ]);
            $lastItem = @$response['hits']['hits'][0]['_source']['search_result_data']['id'];
        } catch (\Exception $e) {
            $lastItem = 0;
        }
        return intval($lastItem);
    }

    private function index_images()
    {
        $index = 'images';
        ini_set('memory_limit', -1);
        $config = config('services.elasticsearch');
        $client = ClientBuilder::create()
            ->setHosts([$config['endpoint']])
            ->setSSLVerification(false)
            ->setBasicAuthentication($config['user'], $config['password'])
            ->build();
        if (date('D') == 'Fri') {
            if (date('H') == 2) {
                // delete index
                try {
                    $params = ['index' => $index];
                    $response = $client->indices()->delete($params);
                } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
                    // do nothing if created
                }
            } else
                return;
        }

        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0, // @note should be 1
                    'analysis' => [
                        "filter" => [
                            "arabic_stop" => [
                                "type" => "stop",
                                "stopwords" => "_arabic_"
                            ],
                            "arabic_keywords" => [
                                "type" => "keyword_marker",
                                "keywords" => ["مثال", "الرياض"]
                            ],
                            "arabic_stemmer" => [
                                "type" => "stemmer",
                                "language" => "arabic"
                            ],
                            "english_stop" => [
                                "type" => "stop",
                                "stopwords" => "_english_"
                            ],
                            "english_stemmer" => [
                                "type" => "stemmer",
                                "language" => "english"
                            ],
                            "english_possessive_stemmer" => [
                                "type" => "stemmer",
                                "language" => "possessive_english"
                            ]
                        ],
                        'analyzer' => [
                            'lowercase_keyword_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase']
                            ],
                            "arabsstock_analyzer" => [
                                "tokenizer" => "standard",
                                "filter" => [
                                    "lowercase",
                                    "decimal_digit",
                                    "arabic_stop",
                                    "english_stop",
                                    "arabic_normalization",
                                    "arabic_keywords",
                                    "arabic_stemmer",
                                    "english_stemmer",
                                    "english_possessive_stemmer",

                                ]
                            ],
                            'suggestion_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase']
                            ]
                        ],

                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'char_filter' => [],
                                'filter' => ['lowercase']
                            ],
                        ],
                    ],
                    'index' => ['max_result_window' => 1000000],
                ],
                'mappings' => [
                    'properties' => [
                        'id' => [
                            'type' => 'long',
                        ],
                    ],
                    'dynamic_templates' => [
                        [
                            'search_result_data' => [
                                'path_match' => 'search_result_data.*',
                                'mapping' => [
                                    "type" => "text",

                                    // 'index' => false,
                                    "analyzer" => "arabsstock_analyzer"
                                ],
                            ]
                        ],
                    ],
                    'properties' => [
                        'search_result_data' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => [
                                    'type' => 'long',
                                ],
                            ]
                        ],
                        'type' => [
                            'type' => 'keyword',
                        ],
                        'store' => [
                            'type' => 'keyword',
                        ],
                        'is_active' => [
                            'type' => 'boolean',
                        ],
                        'active_from' => [
                            'type' => 'date',
                        ],
                        'active_to' => [
                            'type' => 'date',
                        ],
                        'locale' => [
                            'type' => 'keyword',
                        ],
                        'full_text' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer",
                            "term_vector" => "with_positions_offsets"
                        ],
                        'full_text_boosted' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer"

                        ],
                        'string_facet' => [
                            'type' => 'nested',
                            'properties' => [
                                'facet_name' => [
                                    'type' => 'keyword'
                                ],
                                'facet_value' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'completion_terms' => [
                            'type' => 'completion',
                            "analyzer" => "whitespace",
                        ],
                        'suggestion_terms' => [
                            'type' => 'text',
                            'analyzer' => 'suggestion_analyzer'
                        ],
                        'categories' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];


        try {
            $client->indices()->create($params);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) {
            // do nothing if created
        }
        $lastItem = $this->elastic_last_item($client, $index);

        Image::tinySelection()->withoutGlobalScopes(['default_loaded_relations', 'is_liked'])->whereHas('categories')->with('tags', 'categories')
            ->where('status', 'active')->where('id', '>', $lastItem)->orderBy('id', 'desc')->chunk(200, function ($images) use ($client, $index) {
                foreach ($images as $image) {

                    $image->tags_ar = $image->tags->where('local', 'ar')->pluck('title')->toArray();
                    $image->tags_en = $image->tags->where('local', 'en')->pluck('title')->toArray();

                    foreach (['ar', 'en'] as $lang) {
                        $params = [
                            'index' => $index,
                            'id' => $image->id . $lang,
                            'body' => [
                                'type' => 'image',
                                'search_result_data' => [
                                    'id' => $image->id,
                                ],
                                'full_text' => $image->{'title_' . $lang} . ' ' . implode(' ', $image->{'tags_' . $lang}),
                                'full_text_boosted' => $image->{'title_' . $lang} . ' ' . implode(' ', $image->{'tags_' . $lang}),
                                'completion_terms' => [
                                    'input' => $image->{'tags_' . $lang},
                                    'weight' => 34,

                                ],
                                'suggestion_terms' => [
                                    $image->{'title_' . $lang}
                                ],
                                'locale' => $lang,
                                'active_to' => '2050-01-01',
                                'active_from' => '2020-01-01',
                                'categories' => $image->categories->pluck('id')->toArray()
                            ],
                        ];

                        // Document will be indexed to my_index/_doc/my_id
                        $response = $client->index($params);
                    }
                }
            });

        Log::channel('info')->info("End PrepareElasticsearch Images");
    }

    private function index_videos()
    {
        ini_set('memory_limit', -1);
        $index = 'videos';
        $config = config('services.elasticsearch');
        $client = ClientBuilder::create()
            ->setHosts([$config['endpoint']])
            ->setSSLVerification(false)
            ->setBasicAuthentication($config['user'], $config['password'])
            ->build();
        if (date('D') == 'Fri') {
            if (date('H') == 2) {
                // delete index
                try {
                    $params = ['index' => $index];
                    $response = $client->indices()->delete($params);
                } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
                    // do nothing if created
                }
            } else
                return;
        }


        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0, // @note should be 1
                    'analysis' => [
                        "filter" => [
                            "arabic_stop" => [
                                "type" => "stop",
                                "stopwords" => "_arabic_"
                            ],
                            "arabic_keywords" => [
                                "type" => "keyword_marker",
                                "keywords" => ["مثال", "الرياض"]
                            ],
                            "arabic_stemmer" => [
                                "type" => "stemmer",
                                "language" => "arabic"
                            ],
                            "english_stop" => [
                                "type" => "stop",
                                "stopwords" => "_english_"
                            ],
                            "english_stemmer" => [
                                "type" => "stemmer",
                                "language" => "english"
                            ],
                            "english_possessive_stemmer" => [
                                "type" => "stemmer",
                                "language" => "possessive_english"
                            ]
                        ],
                        'analyzer' => [
                            'lowercase_keyword_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase']
                            ],
                            "arabsstock_analyzer" => [
                                "tokenizer" => "standard",
                                "filter" => [
                                    "lowercase",
                                    "decimal_digit",
                                    "arabic_stop",
                                    "english_stop",
                                    "arabic_normalization",
                                    "arabic_keywords",
                                    "arabic_stemmer",
                                    "english_stemmer",
                                    "english_possessive_stemmer"

                                ]
                            ],
                            'suggestion_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase']
                            ]
                        ],

                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'char_filter' => [],
                                'filter' => ['lowercase']
                            ],
                        ],
                    ],
                    'index' => ['max_result_window' => 1000000],

                ],
                'mappings' => [
                    'properties' => [
                        'id' => [
                            'type' => 'long',
                        ],
                    ],
                    'dynamic_templates' => [
                        [
                            'search_result_data' => [
                                'path_match' => 'search_result_data.*',
                                'mapping' => [
                                    "type" => "text",

                                    // 'index' => false,
                                    "analyzer" => "arabsstock_analyzer"
                                ],
                            ]
                        ],
                    ],
                    'properties' => [
                        'search_result_data' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => [
                                    'type' => 'long',
                                ],
                            ]
                        ],
                        'type' => [
                            'type' => 'keyword',
                        ],
                        'store' => [
                            'type' => 'keyword',
                        ],
                        'is_active' => [
                            'type' => 'boolean',
                        ],
                        'active_from' => [
                            'type' => 'date',
                        ],
                        'active_to' => [
                            'type' => 'date',
                        ],
                        'locale' => [
                            'type' => 'keyword',
                        ],
                        'full_text' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer",
                            "term_vector" => "with_positions_offsets"
                        ],
                        'full_text_boosted' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer"

                        ],
                        'string_facet' => [
                            'type' => 'nested',
                            'properties' => [
                                'facet_name' => [
                                    'type' => 'keyword'
                                ],
                                'facet_value' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'completion_terms' => [
                            'type' => 'completion',
                            "analyzer" => "whitespace",
                        ],
                        'suggestion_terms' => [
                            'type' => 'text',
                            'analyzer' => 'suggestion_analyzer'
                        ],
                        'categories' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];


        try {
            $client->indices()->create($params);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) {
            // do nothing if created
        }

        $lastItem = $this->elastic_last_item($client, $index);

        Video::tinySelection()->withoutGlobalScopes(['default_loaded_relations', 'is_liked'])->whereHas('categories')->with('tags', 'categories')
            ->where('id', '>', $lastItem)->where('parent_id', '=', null)->orderBy('id', 'desc')->where('status', 'active')->chunk(200, function ($videos) use ($client, $index) {
                foreach ($videos as $video) {

                    $video->tags_ar = $video->tags->where('local', 'ar')->pluck('title')->toArray();
                    $video->tags_en = $video->tags->where('local', 'en')->pluck('title')->toArray();
                    foreach (['ar', 'en'] as $lang) {
                        $params = [
                            'index' => $index,
                            'id' => $video->id . $lang,
                            'body' => [
                                'type' => 'video',
                                'search_result_data' => [
                                    'id' => $video->id,
                                ],
                                'full_text' => $video->{'title_' . $lang} . ' ' . implode(' ', $video->{'tags_' . $lang}),
                                'full_text_boosted' => $video->{'title_' . $lang} . ' ' . implode(' ', $video->{'tags_' . $lang}),
                                'completion_terms' => [
                                    'input' => $video->{'tags_' . $lang},
                                    'weight' => 34,

                                ],
                                'suggestion_terms' => [
                                    $video->{'title_' . $lang}
                                ],
                                'locale' => $lang,
                                'active_to' => '2050-01-01',
                                'active_from' => '2020-01-01',
                                'categories' => $video->categories->pluck('id')->toArray()
                            ],
                        ];

                        // Document will be indexed to my_index/_doc/my_id
                        $response = $client->index($params);
                    }
                }
            });

        Log::channel('info')->info("End PrepareElasticsearch Videos");


    }

    private function index_vectors()
    {
        $index = 'vectors';
        $config = config('services.elasticsearch');
        $client = ClientBuilder::create()
            ->setHosts([$config['endpoint']])
            ->setSSLVerification(false)
            ->setBasicAuthentication($config['user'], $config['password'])
            ->build();
        ini_set('memory_limit', -1);
        if (date('D') == 'Fri') {
            if (date('H') == 2) {
                // delete index
                try {
                    $params = ['index' => $index];
                    $response = $client->indices()->delete($params);
                } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
                    // do nothing if created
                }
            } else
                return;
        }

        $params = [
            'index' => 'vectors',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0, // @note should be 1
                    'analysis' => [
                        "filter" => [
                            "arabic_stop" => [
                                "type" => "stop",
                                "stopwords" => "_arabic_"
                            ],
                            "arabic_keywords" => [
                                "type" => "keyword_marker",
                                "keywords" => ["مثال", "الرياض"]
                            ],
                            "arabic_stemmer" => [
                                "type" => "stemmer",
                                "language" => "arabic"
                            ],
                            "english_stop" => [
                                "type" => "stop",
                                "stopwords" => "_english_"
                            ],
                            "english_stemmer" => [
                                "type" => "stemmer",
                                "language" => "english"
                            ],
                            "english_possessive_stemmer" => [
                                "type" => "stemmer",
                                "language" => "possessive_english"
                            ]
                        ],
                        'analyzer' => [
                            'lowercase_keyword_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase']
                            ],
                            "arabsstock_analyzer" => [
                                "tokenizer" => "standard",
                                "filter" => [
                                    "lowercase",
                                    "decimal_digit",
                                    "arabic_stop",
                                    "english_stop",
                                    "arabic_normalization",
                                    "arabic_keywords",
                                    "arabic_stemmer",
                                    "english_stemmer",
                                    "english_possessive_stemmer"

                                ]
                            ],
                            'suggestion_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase']
                            ]
                        ],

                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'char_filter' => [],
                                'filter' => ['lowercase']
                            ],
                        ],
                    ],
                    'index' => ['max_result_window' => 1000000],

                ],
                'mappings' => [
                    'properties' => [
                        'id' => [
                            'type' => 'long',
                        ],
                    ],
                    'dynamic_templates' => [
                        [
                            'search_result_data' => [
                                'path_match' => 'search_result_data.*',
                                'mapping' => [
                                    "type" => "text",

                                    // 'index' => false,
                                    "analyzer" => "arabsstock_analyzer"
                                ],
                            ]
                        ],
                    ],
                    'properties' => [
                        'search_result_data' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => [
                                    'type' => 'long',
                                ],
                            ]
                        ],
                        'type' => [
                            'type' => 'keyword',
                        ],
                        'store' => [
                            'type' => 'keyword',
                        ],
                        'is_active' => [
                            'type' => 'boolean',
                        ],
                        'active_from' => [
                            'type' => 'date',
                        ],
                        'active_to' => [
                            'type' => 'date',
                        ],
                        'locale' => [
                            'type' => 'keyword',
                        ],
                        'full_text' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer",
                            "term_vector" => "with_positions_offsets"
                        ],
                        'full_text_boosted' => [
                            'type' => 'text',
                            "analyzer" => "arabsstock_analyzer"

                        ],
                        'string_facet' => [
                            'type' => 'nested',
                            'properties' => [
                                'facet_name' => [
                                    'type' => 'keyword'
                                ],
                                'facet_value' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'completion_terms' => [
                            'type' => 'completion',
                            "analyzer" => "whitespace",
                        ],
                        'suggestion_terms' => [
                            'type' => 'text',
                            'analyzer' => 'suggestion_analyzer'
                        ],
                        'categories' => [
                            'type' => 'keyword'
                        ]
                    ]
                ]
            ]
        ];


        try {
            $client->indices()->create($params);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) {
            // do nothing if created
        }
        $lastItem = $this->elastic_last_item($client, $index);

        Vector::tinySelection()->withoutGlobalScopes(['default_loaded_relations', 'is_liked'])->whereHas('categories')->with('tags', 'categories')
            ->where('id', '>', $lastItem)->where('status', 'active')->orderBy('id', 'desc')->chunk(200, function ($vectors) use ($client, $index) {
                foreach ($vectors as $vector) {

                    $vector->tags_ar = $vector->tags->where('local', 'ar')->pluck('title')->toArray();
                    $vector->tags_en = $vector->tags->where('local', 'en')->pluck('title')->toArray();

                    foreach (['ar', 'en'] as $lang) {
                        $params = [
                            'index' => 'vectors',
                            'id' => $vector->id . $lang,
                            'body' => [
                                'type' => 'vector',
                                'search_result_data' => [
                                    'id' => $vector->id,
                                ],
                                'full_text' => $vector->{'title_' . $lang} . ' ' . implode(' ', $vector->{'tags_' . $lang}),
                                'full_text_boosted' => $vector->{'title_' . $lang} . ' ' . implode(' ', $vector->{'tags_' . $lang}),
                                'completion_terms' => [
                                    'input' => $vector->{'tags_' . $lang},
                                    'weight' => 34,

                                ],
                                'suggestion_terms' => [
                                    $vector->{'title_' . $lang}
                                ],
                                'locale' => $lang,
                                'active_to' => '2050-01-01',
                                'active_from' => '2020-01-01',
                                'categories' => $vector->categories->pluck('id')->toArray()
                            ],
                        ];

                        // Document will be indexed to my_index/_doc/my_id
                        $response = $client->index($params);
                    }
                }
            });
    }
}
