<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\AdminPlugins;

use MohsenJS\Tools;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use MohsenJS\Plugins\AdminPlugin;
use Amp\Http\Client\HttpClientBuilder;

final class EvalcodePlugin extends AdminPlugin
{
    /**
     * Rextester API url.
     */
    protected const REXTESTER_URL = 'https://rextester.com/rundotnet/api';

    /**
     * List of all supported programming languages.
     */
    protected const LANGS = [
        'c#'                  => 1,
        'csharp'              => 1,
        'vb.net'              => 2,
        'vb'                  => 2,
        'visual_basic_dotnet' => 2,
        'f#'                  => 3,
        'fsharp'              => 3,
        'java'                => 4,
        'python2'             => 5,
        'py2'                 => 5,
        'c_gcc'               => 6,
        'gcc'                 => 6,
        'c'                   => 6,
        'cplusplus_gcc'       => 7,
        'cplusplus'           => 7,
        'g++'                 => 7,
        'c++'                 => 7,
        'cpp_gcc'             => 7,
        'cpp'                 => 7,
        'php'                 => 8,
        'pascal'              => 9,
        'pas'                 => 9,
        'fpc'                 => 9,
        'objective_c'         => 10,
        'objc'                => 10,
        'haskell'             => 11,
        'ruby'                => 12,
        'perl'                => 13,
        'lua'                 => 14,
        'nasm'                => 15,
        'asm'                 => 15,
        'sql_server'          => 16,
        'v8'                  => 17,
        'common_lisp'         => 18,
        'clisp'               => 18,
        'lisp'                => 18,
        'prolog'              => 19,
        'golang'              => 20,
        'go'                  => 20,
        'scala'               => 21,
        'scheme'              => 22,
        'node'                => 23,
        'javascript'          => 23,
        'js'                  => 23,
        'python3'             => 24,
        'py3'                 => 24,
        'python'              => 24,
        'c_clang'             => 26,
        'clang'               => 26,
        'cplusplus_clang'     => 27,
        'cpp_clang'           => 27,
        'clangplusplus'       => 27,
        'clang++'             => 27,
        'visual_cplusplus'    => 28,
        'visual_cpp'          => 28,
        'vc++'                => 28,
        'msvc'                => 28,
        'visual_c'            => 29,
        'd'                   => 30,
        'r'                   => 31,
        'tcl'                 => 32,
        'mysql'               => 33,
        'postgresql'          => 34,
        'oracle'              => 35,
        'swift'               => 37,
        'bash'                => 38,
        'ada'                 => 39,
        'erlang'              => 40,
        'elixir'              => 41,
        'ocaml'               => 42,
        'kotlin'              => 43,
        'brainfuck'           => 44,
        'fortran'             => 45,
    ];

    /**
     * Compiler arguments.
     */
    protected const COMPILER_ARGS = [
        '6'  => '-Wall -std=gnu99 -O2 -o a.out source_file.c',
        '7'  => '-Wall -std=c++14 -O2 -o a.out source_file.cpp',
        '11' => '-o a.out source_file.hs',
        '20' => '-o a.out source_file.go',
        '26' => '-Wall -std=gnu99 -O2 -o a.out source_file.c',
        '27' => '-Wall -std=c++14 -stdlib=libc++ -O2 -o a.out source_file.cpp',
        '28' => 'source_file.cpp -o a.exe /EHsc /MD /I C=>\\boost_1_60_0 /link /LIBPATH=>C=>\\boost_1_60_0\\stage\\lib',
        '29' => 'source_file.c -o a.exe',
        '30' => 'source_file.d -ofa.out',
    ];

    /**
     * Ok http status code.
     */
    protected const OK_HTTP_STATUS_CODE = 200;

    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'evalcode';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'compile and run code';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]eval ([\w.#+]+)\s+([\s\S]+?)(?:\s+\/stdin\s+([\s\S]+))?$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!eval <lang> <code> </stdin "input">';

    public function execute(): \Generator
    {
        $message = yield $this->getResultMessage();

        $message === '' && $message = 'An error occurred :(';

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getUpdate(),
            'message'         => $message,
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
            'parse_mode'      => 'HTML',
        ]);
    }

    /**
     * Get the programming language code.
     *
     * @return int|null language code, `null` otherwise.
     */
    private function getLangCode(): ?int
    {
        $lang = \strtolower($this->getMatches()[1]);

        return self::LANGS[$lang] ?? null;
    }

    /**
     * Run the code and return the result.
     *
     * @param array $params
     *
     * @return \Generator
     */
    private function runCode(array $params): \Generator
    {
        $client  = HttpClientBuilder::buildDefault();
        $request = new Request(self::REXTESTER_URL, 'POST');
        $request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request->setBody(\http_build_query($params));

        /**
         * @var Response $response
         */
        $response = yield $client->request($request);
        if ($response->getStatus() === self::OK_HTTP_STATUS_CODE) {
            return \json_decode(yield $response->getBody()->buffer(), true);
        }

        return null;
    }

    /**
     * Get Rextester API parameters.
     *
     * @param int $langCode
     *
     * @return array
     */
    private function getParams(int $langCode): array
    {
        return [
            'LanguageChoice' => $langCode,
            'Program'        => $this->getMatches()[2],
            'Input'          => $this->getMatches()[3] ?? null,
            'CompilerArgs'   => self::COMPILER_ARGS[$langCode] ?? null,
        ];
    }

    /**
     * Get result message.
     *
     * @return \Generator
     */
    private function getResultMessage(): \Generator
    {
        $langCode = $this->getLangCode();
        if ($langCode === null) {
            return 'The entered programming language was not found.';
        }

        $message = '';
        $result  = yield $this->runCode($this->getParams($langCode));
        foreach (['Result', 'Errors', 'Warnings'] as $key) {
            if ($result[$key] ?? false) {
                $message .= \sprintf(
                    '<b>%s:</b><br><pre>%s</pre><br><br>',
                    $key,
                    Tools::clean($result[$key], 'html')
                );
            }
        }

        return $message;
    }
}
