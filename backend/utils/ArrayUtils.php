<?php
class ArrayUtils {
    public static function mapByColumn(array $array, string $column): array {
        $result = [];
        foreach ($array as $item) {
            $result[$item[$column]] = $item;
        }
        return $result;
    }
}