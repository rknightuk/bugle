<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;

class TootFormatter {

    public static function format($content)
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $content = $converter->convert($content)->getContent();

        $content = nl2br($content);
        $content = str_replace("\n", '', $content);

        $tags = [];

        $pattern = '/@?\b([A-Z0-9._%+-]+)@([A-Z0-9.-]+\.[A-Z]{2,})\b/mi';
        preg_match_all($pattern, $content, $matches);
        [$fullUsernames, $usernames, $domains] = $matches;

        foreach ($fullUsernames as $i => $fu) {
            $tags[] = [
                'type' => 'Mention',
                'href' => 'https://' . $domains[$i] . '/@' . $usernames[$i],
                'name' => $fu,
            ];

            $content = str_replace(
                $fu,
                sprintf(
                    '<span class="h-card"><a href="https://%s/@%s" class="u-url mention">@<span>%s</span></a></span>',
                    $domains[$i],
                    $usernames[$i],
                    $usernames[$i]
                ),
                $content,
            );
        }

        $content = preg_replace('/(<br \/>)+$/', '', $content);

        return [
            $content, $tags
        ];
    }
}
