<?php
namespace cmpl\util;

class Inflector
{
    private static $_ignore = array(
        'Amoyese', 'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
        'carp', 'chassis', 'clippers', 'cod', 'coitus', 'Congoese', 'contretemps', 'corps',
        'debris', 'diabetes', 'djinn', 'eland', 'elk', 'equipment', 'Faroese', 'flounder',
        'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
        'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings',
        'jackanapes', 'Kiplingese', 'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media',
        'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese',
        'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese',
        'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors',
        'sea[- ]bass', 'series', 'Shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
        'trousers', 'trout','tuna', 'Vermontese', 'Wenchowese', 'whiting', 'wildebeest',
        'Yengeese'
    );

    private static $_singular = array(
        'rules' => array(
            '/^(p)eople$/i'   => '$1erson',
            '/^(c)hildren$/i' => '$1hild',
            '/xes$/i'         => 'x',
            '/(r|t)ies$/i'    => '$1y',
            '/sses$/i'        => 'ss',
            '/ss$/i'          => 'ss',
            '/s$/i'           => ''
        )
    );

    private static $_plural = array(
        'rules' => array(
            '/^(p)erson$/i' => '$1eople',
            '/^(c)hild/i'   => '$1hildren',
            '/x$/i'         => 'xes',
            '/(r|t)y$/i'    => '$1ies',
            '/ss$/i'        => 'sses',
            '/([^s])s$/i'   => '$1s',
            '/$/'           => 's'
        ),

        'irregular' => array()
    );

    /*
     * Cache
     */

    private static $_underscored = array();
    private static $_camelized = array();
    private static $_tableized = array();
    private static $_classified = array();
    private static $_singularized = array();
    private static $_pluralized = array();
    private static $_sluggified = array();
    private static $_searchabled = array();
    private static $_humanized = array();
    private static $_urlized = array();

    private static $_cache = array();


    public static function underscore($string)
    {
        if (!isset(static::$_underscored[$string]))
        {
            static::$_underscored[$string] = strtolower(preg_replace('/(?<=\w)([A-Z][a-z0-9]+)/', '_$1', $string));
        }

        return static::$_underscored[$string];
    }

    public static function camelize($string)
    {
        if (!isset(static::$_camelized[$string]))
        {
            static::$_camelized[$string] = str_replace(' ', '', ucwords(preg_replace('/[^a-z0-9]+/i', ' ', $string)));
        }

        return static::$_camelized[$string];
    }

    public static function tableize($string)
    {
        if (!isset(static::$_tableized[$string]))
        {
            return static::$_tableized[$string] = static::pluralize(static::underscore($string));
        }

        return static::$_tableized[$string];
    }

    public static function classify($string)
    {
        if (!isset(static::$_classified[$string]))
        {
            static::$_classified[$string] = static::singularize(static::camelize($string));
        }

        return static::$_classified[$string];
    }

    public static function singularize($string)
    {
        if (!isset(static::$_singularized[$string]))
        {
            $regexIgnore = static::_enclose(static::$_ignore);

            if (preg_match('/^' . $regexIgnore . '$/i', $string))
            {
                return static::$_singularized[$string] = $string;
            }

            foreach (static::$_singular['rules'] as $pattern => $replacement)
            {
                if (preg_match($pattern, $string, $matches))
                {
                    return static::$_singularized[$string] = preg_replace($pattern, $replacement, $string);
                }
            }
        }

        return static::$_singularized[$string];
    }

    public static function pluralize($string)
    {
        if (!isset(static::$_pluralized[$string]))
        {
            $regexIgnore = static::_enclose(static::$_ignore);

            if (preg_match('/^' . $regexIgnore . '$/i', $string))
            {
                return static::$_pluralized[$string] = $string;
            }

            foreach (static::$_plural['rules'] as $pattern => $replacement)
            {
                if (preg_match($pattern, $string, $matches))
                {
                    return static::$_pluralized[$string] = preg_replace($pattern, $replacement, $string);
                }
            }
        }

        return static::$_pluralized[$string];
    }

    public static function sluggify($string)
    {
        if (!isset(static::$_sluggified[$string]))
        {
            static::$_sluggified[$string] = preg_replace('/[^a-z0-9]+/', '-', strtolower($string));
        }

        return static::$_sluggified[$string];
    }

    public static function urlize($string)
    {
        if (!isset(static::$_urlized[$string]))
        {
            $urlized = trim($string);
            $urlized = mb_convert_kana($urlized, 'KVa', 'UTF-8');
            $urlized = preg_replace('/^[、。！？（）「」『』【】]+$/u', ' ', $urlized);
            $urlized = preg_replace('/[^0-9a-z一-龠々ヵヶぁ-んァ-ヴー_-]+/iu', '-', $urlized);
            $urlized = trim($urlized, '-');

            static::$_urlized[$string] = $urlized;
        }

        return static::$_urlized[$string];
    }

    public static function humanize($string, $separator = '_')
    {
        if (!isset(static::$_humanized[$string . $separator]))
        {
            static::$_humanized[$string . $separator] = ucwords(str_replace($separator, ' ', $string));
        }

        return static::$_humanized[$string . $separator];
    }

    public static function searchable($string)
    {
        if (!isset(static::$_searchabled[$string]))
        {
            static::$_searchabled[$string] = str_replace('-', ' ', static::urlize(strtolower($string)));
        }

        return static::$_searchabled[$string];
    }

    public static function reset()
    {
        static::$_underscored  =
        static::$_camelized    =
        static::$_classified   =
        static::$_pluralized   =
        static::$_singularized =
        static::$_tableized    =
        static::$_humanized    =
        static::$_searchabled  =
        static::$_sluggified   = array();
    }

    protected static function _enclose(array $list)
    {
        return '(?:' . implode('|', $list) . ')';
    }
}
