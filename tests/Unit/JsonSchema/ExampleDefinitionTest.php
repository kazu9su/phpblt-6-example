<?php
namespace Tests\Unit\JsonSchema;

use App\Validator\ExampleValidator;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;

class ExampleDefinitionTest extends \Tests\TestCase
{
    /**
     * @var ExampleValidator
     */
    private $validator;

    /**
     * @var RefResolver
     */
    private $refResolver;

    private $def_path;

    function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->validator = $this->app->make('App\Validator\ExampleValidator');
        $this->refResolver = new RefResolver($this->validator->getUriRetriever(), new UriResolver());
        $this->def_path = env('ROOT_DIR') . ExampleValidator::FILE;
    }

    function test_id_valid()
    {
        $this->validator->check((object) [
            'id' => 1,
        ], $this->get_schema('id'));

        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider id_provider
     */
    function test_id_invalid($value)
    {
        $this->validator->check((object) [
            'id' => $value,
        ], $this->get_schema('id'));

        $this->assertFalse($this->validator->isValid());
    }

    function id_provider()
    {
        return [
            ['hoge'],
            ['1'],
            ['$ref:hogehoge']
        ];
    }

    function test_string_valid()
    {
        $this->validator->check((object) [
            'string' => 'hoge',
        ], $this->get_schema('string'));

        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider string_provider
     */
    function test_string_invalid($value)
    {
        $this->validator->check((object) [
            'string' => $value,
        ], $this->get_schema('string'));

        $this->assertFalse($this->validator->isValid());
    }

    function string_provider()
    {
        return [
            [1],
            [['hoge']],
            [(object)['hoge']],
        ];
    }

    function test_object_valid()
    {
        $this->validator->check((object) [
            'object' => (object) [
                'hoge' => 'aa',
                'fuga' => 'bb',
            ],
        ], $this->get_schema('object'));

        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider object_provider
     */
    function test_object_invalid($value)
    {
        $this->validator->check((object) [
            'object' => $value,
        ], $this->get_schema('object'));

        $this->assertFalse($this->validator->isValid());
    }

    function object_provider()
    {
        return [
            [1],
            [['hoge']],
            ['11']
        ];
    }

    function test_array_valid()
    {
        $this->validator->check((object) [
            'array' => [
                'hoge', 'fuga', '1', '2'
            ],
        ], $this->get_schema('array'));

        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider array_provider
     */
    function test_array_invalid($value)
    {
        $this->validator->check((object) [
            'array' => $value,
        ], $this->get_schema('array'));

        $this->assertFalse($this->validator->isValid());
    }

    function array_provider()
    {
        return [
            [1],
            ['hoge'],
            [(object)['hoge']]
        ];
    }

    function get_schema($property)
    {
        $schema = $this->refResolver->resolve('file://' . $this->def_path);

        return (object) [$property => $schema->properties->{$property}];
    }
}
