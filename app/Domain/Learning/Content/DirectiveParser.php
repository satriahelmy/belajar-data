<?php

namespace App\Domain\Learning\Content;

class DirectiveParser
{
    private const OPENING_PATTERN = '/^:::(?<name>[a-z][a-z0-9-]*)(?<attributes>(?:\s+[a-z][a-z0-9_-]*="[^"\r\n]*")*)\s*$/';

    /**
     * @param callable(string, array<string, string>, string): string $resolver
     * @return array{markdown: string, blocks: array<string, string>}
     */
    public function replace(string $markdown, callable $resolver): array
    {
        $lines = preg_split('/\r?\n/', $markdown);
        $output = [];
        $blocks = [];
        $lineCount = count($lines);

        for ($index = 0; $index < $lineCount; $index++) {
            $line = trim($lines[$index]);

            if ($line === '' || ! str_starts_with($line, ':::')) {
                $output[] = $lines[$index];
                continue;
            }

            if (! preg_match(self::OPENING_PATTERN, $line, $matches)) {
                throw new ContentValidationException('Invalid directive syntax near line '.($index + 1).'.');
            }

            $name = $matches['name'];
            $attributes = $this->parseAttributes($matches['attributes'], $index + 1);
            $body = [];
            $closed = false;

            for ($index++; $index < $lineCount; $index++) {
                if (trim($lines[$index]) === ':::') {
                    $closed = true;
                    break;
                }

                $body[] = $lines[$index];
            }

            if (! $closed) {
                throw new ContentValidationException("Unclosed [{$name}] directive near line ".($index + 1).'.');
            }

            $token = "BELAJARDATA_BLOCK_{$name}_".count($blocks);
            $blocks[$token] = $resolver($name, $attributes, trim(implode("\n", $body)));
            $output[] = $token;
        }

        return [
            'markdown' => implode("\n", $output),
            'blocks' => $blocks,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function parseAttributes(string $source, int $line): array
    {
        $attributes = [];
        $offset = 0;

        while ($offset < strlen($source)) {
            if (! preg_match('/\G\s+(?<name>[a-z][a-z0-9_-]*)="(?<value>[^"\r\n]*)"/A', $source, $matches, 0, $offset)) {
                throw new ContentValidationException("Invalid directive attributes near line {$line}.");
            }

            if (isset($attributes[$matches['name']])) {
                throw new ContentValidationException("Duplicate directive attribute [{$matches['name']}] near line {$line}.");
            }

            $attributes[$matches['name']] = $matches['value'];
            $offset += strlen($matches[0]);
        }

        return $attributes;
    }
}
