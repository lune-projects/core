<?php

namespace Lune\Framework\Core\Util;

class ClassFileUtils
{

    public static function getClassFullNameFromFile(string $filePathName): ?string
    {
        $sourceCode = file_get_contents($filePathName);
        $namespace = ClassFileUtils::getClassNamespaceFromFile($filePathName, $sourceCode);
        $className = ClassFileUtils::getClassNameFromFile($filePathName, $sourceCode);

        if ($namespace !== null && $className !== null) {
            return "$namespace\\$className";
        }

        return null;
    }

    public static function getClassObjectFromFile(string $filePathName)
    {
        $classString = ClassFileUtils::getClassFullNameFromFile($filePathName);

        $object = new $classString;

        return $object;
    }

    public static function getClassNamespaceFromFile(string $filePathName, string $sourceCode = null): ?string
    {
        $sourceCode = $sourceCode ?? file_get_contents($filePathName);

        $tokens = token_get_all($sourceCode);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $found = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Namespace declaration found
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $found = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (!$found) {
            return null;
        } else {
            return $namespace;
        }
    }

    public static function getClassNameFromFile(string $filePathName, string $sourceCode = null): ?string
    {
        $sourceCode = $sourceCode ?? file_get_contents($filePathName);

        $classes = array();
        $tokens = token_get_all($sourceCode);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {

                $className = $tokens[$i][1];
                $classes[] = $className;
            }
        }

        return $classes[0];
    }
}
