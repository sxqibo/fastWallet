<?php

namespace Sxqibo\FastWallet;

class Install
{
    /**
     * @var array
     */
    protected static array $pathRelation = array(
        'database/migrations' => 'database/migrations',
        'database/seeds'      => 'database/seeds',
    );

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        static::installByRelation();

        var_dump('安装完成，提示：1.请手动执行php think migrate:run 和 php think seed:run -s InitWalletAccountSeeder');
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        var_dump('uninstall');
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = root_path() . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }

            static::copyDir(__DIR__ . "/$source", root_path() . "$dest");
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {

    }

    /**
     * Copy dir.
     * @param $source
     * @param $dest
     * @param bool $overwrite
     * @return void
     */
    protected static function copyDir($source, $dest, $overwrite = false)
    {
        if (is_dir($source)) {
            if (!is_dir($dest)) {
                mkdir($dest);
            }
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file !== "." && $file !== "..") {
                    static::copyDir("$source/$file", "$dest/$file");
                }
            }
        } else if (file_exists($source) && ($overwrite || !file_exists($dest))) {
            copy($source, $dest);
        }
    }

}
