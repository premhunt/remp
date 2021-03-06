<?php
namespace Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Remp\MailerModule\Generators\GenericBestPerformingArticlesGenerator;
use Remp\MailerModule\PageMeta\ContentInterface;
use Remp\MailerModule\PageMeta\GenericPageContent;
use Remp\MailerModule\PageMeta\Meta;
use Remp\MailerModule\PageMeta\TransportInterface;
use Remp\MailerModule\Repository\SourceTemplatesRepository;

class GenericBestPerformingArticlesGeneratorTest extends TestCase
{
    private $sourceTemplateRepository;

    protected function setUp()
    {
        $htmlContent = <<<TEMPLATE
<table>        
{% for url,item in items %}
    <tr>
        <td>{{ item.title }}</td>
        <td>{{ item.description }}</td>
        <td><img src="{{item.image}}"></td>
        <td>{{ url }}</td>
    </tr>
{% endfor %}
</table>
TEMPLATE;

        $textContent = <<<TEMPLATE
{% for url,item in items %}
{{ item.title }}
{{ item.description }}
{{ url}}
{% endfor %}
TEMPLATE;

        $mailSourceTemplate = [
            "content_html" => $htmlContent,
            "content_text" => $textContent
        ];

        $this->sourceTemplateRepository = $this->createConfiguredMock(SourceTemplatesRepository::class, [
            'find' => (object) $mailSourceTemplate
        ]);
    }

    public function testProcess()
    {
        $transport = new class() implements TransportInterface
        {
            public function getContent($url)
            {
                return <<<HTML
<!DOCTYPE html>
<html lang="sk">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
    <meta charset="utf-8">
    <meta property="og:title" content="here_is_title">
    <meta property="og:description" content="here_is_description">
    <meta property="og:something" content="THIS_META_WONT_BE_IN_EMAIL">
    <meta property="og:url" content="https://www.tyzden.sk/nazory/48501/zachrani-vas-zachranka-bez-lekara/">
    <meta name="author" content="Andrej Bán">
    <meta property="og:image" content="https://page.com/someimage.jpg">
</head>
<body>
  <header>header</header>
  <div>THIS_TEXT_WONT_BE_IN_EMAIL</div>
  <footer>footer</footer>
  </body></html>
HTML;
            }
        };

        $generator = new GenericBestPerformingArticlesGenerator($this->sourceTemplateRepository, new GenericPageContent($transport));

        $testObj = new \stdClass();
        $testObj->source_template_id = 1;
        $testObj->articles = "http://someurl.com";

        $output = $generator->process($testObj);

        $htmlOutput = $output['htmlContent'];
        $textOutput = $output['textContent'];

        self::assertContains("here_is_title", $htmlOutput);
        self::assertContains("here_is_title", $textOutput);

        self::assertContains("here_is_description", $htmlOutput);
        self::assertContains("here_is_description", $textOutput);

        self::assertContains('https://page.com/someimage.jpg', $htmlOutput);
        self::assertNotContains('https://page.com/someimage.jpg', $textOutput);

        self::assertNotContains('THIS_TEXT_WONT_BE_IN_EMAIL', $htmlOutput);
        self::assertNotContains('THIS_TEXT_WONT_BE_IN_EMAIL', $textOutput);

        self::assertNotContains('THIS_META_WONT_BE_IN_EMAIL', $htmlOutput);
        self::assertNotContains('THIS_META_WONT_BE_IN_EMAIL', $textOutput);
    }
}