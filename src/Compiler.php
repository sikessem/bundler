<?php

namespace SIKessEm\Setup;

class Compiler {
    public function __construct(protected string $wdir) {
        $this->wdir = realpath($wdir) . DIRECTORY_SEPARATOR;
    }

    public function compile(string $input, string $output): void {
        self::compileFile(
            file_exists($input) ? realpath($input) : $this->wdir . $input,
            file_exists($output) ? realpath($output) : $this->wdir . $output
        );
    }

    public static function compileDir(string $input, string $output): void {
        if (!is_dir($output))
            mkdir($output, 0777, true);

        foreach(scandir($input) as $file)
            if (!in_array($file, ['.', '..']))
                self::compileFile($input . DIRECTORY_SEPARATOR . $file, $output . DIRECTORY_SEPARATOR . $file);
    }

    public static function compileFile(string $input, string $output): void {
        if (!file_exists($input))
            throw new \RuntimeException("No such file or directory $input");

        if (is_dir($input)) {
            self::compileDir($input, $output);
            return;
        }

        $input = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $input);
        $output = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $output);

        $code = file_get_contents($input);
        $code = self::compileCode($code);

        if (is_dir($output) || (strrpos($output, DIRECTORY_SEPARATOR) === (strlen($output) - 1))) {
            if(!is_dir($output))
                mkdir($output, 0777, true);
            $output = realpath($output);
            $output .= DIRECTORY_SEPARATOR . basename($input);
        }
        fprintf(STDOUT, 'Compile %s to %s' . PHP_EOL, $input, $output);
        file_put_contents($output, $code, );
    }

    public static function compileCode(string $code): string {
        if (!function_exists('token_get_all'))
            return $code;

        $tokens = token_get_all($code);
        $output = '';
        $previous = new Token;
        $next = new Token;
        foreach ($tokens as $key => $token) {
            $next->setValue($tokens[$key + 1] ?? null);
            $token = new Token($token);
            if ($token->isString())
                $output .= $token;
            elseif ($token->in(T_COMMENT, T_DOC_COMMENT))
                $output .= '';
            elseif ($token->is(T_OPEN_TAG))
                $output .= preg_replace('/(\S+)\s+$/s', '$1 ', $token->getContent());
            elseif ($token->is(T_CLOSE_TAG))
                $output .= preg_replace('/\s+(\S+)$/s', ' $1', $token->getContent());
            elseif ($token->is(T_WHITESPACE)) {
                if (
                    (
                        $previous->isNotArray() ||
                        $next->isNotArray()
                    ) || (
                        $previous->in(T_DOUBLE_ARROW, '??=' , '??', '?:', '?') ||
                        $next->is(T_DOUBLE_ARROW, '??=', '??', '?:', '?')
                    ) ||(
                        $previous->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT) ||
                        $next->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT)
                    ) || (
                        $previous->is(T_OPEN_TAG) ||
                        $next->is(T_CLOSE_TAG)
                    )
                ) $output .= '';
                else {
                    $space = $token->getContent();
                    $space = preg_replace('/[ \t]+/', ' ', $space);
                    $space = preg_replace('/[\r\n]+/', ' ', $space);
                    $output .= $space;
                }
            }
            else
                $output .= $token;
            $previous = $token;
        }
        unset($previous, $next, $tokens);
        return $output;
    }
}