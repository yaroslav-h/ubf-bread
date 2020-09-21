<?php

namespace app\commands;


use app\models\Lesson;
use app\rbac\RbacEnum;
use Yii;
use yii\base\DynamicModel;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use Minishlink\WebPush\VAPID;
use yii\helpers\Json;

class AppController extends Controller
{
    /** @var array */
    public $writablePaths = [
        '@app/runtime',
        '@app/web/assets',
    ];

    /** @var array */
    public $executablePaths = [
        '@app/yii',
    ];

    /** @var array */
    public $generateKeysPaths = [
        '@app/.env'
    ];

    /**
     * Sets given keys to .env file
     */
    public function actionSetKeys()
    {
        $this->setKeys($this->generateKeysPaths);
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionSetup()
    {
        $this->runAction('set-writable', ['interactive' => $this->interactive]);
        $this->runAction('set-executable', ['interactive' => $this->interactive]);
        $this->runAction('set-keys', ['interactive' => $this->interactive]);

        Yii::$app->runAction('migrate/up', ['interactive' => $this->interactive]);

        Yii::$app->runAction('migrate/up', [
            'interactive' => $this->interactive,
            'migrationPath' => '@yii/log/migrations/'
        ]);
    }

    public function actionAddUser($email, $password)
    {
        $model = new DynamicModel(compact('email', 'password'));
        $model->addRule(['email'], 'string', ['max' => 128]);
        $model->addRule(['password'], 'string', ['min' => 5]);
        $model->addRule('email', 'email');

        if(!$model->validate()) {
            $this->stdout('Invalid params' . PHP_EOL, Console::FG_RED);
            foreach ($model->firstErrors as $error) {
                $this->stdout($error . PHP_EOL, Console::FG_YELLOW);
            }
            exit;
        }

        if(Yii::$app->db->createCommand()->insert('users', [
            'group' => RbacEnum::DEFAULT_GROUP,
            'name' => explode('@', $email)[0],
            'email' => $email,
            'password_hash' => Yii::$app->security->generatePasswordHash($password),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'created_at' => time()
        ])->execute()) {
            $this->stdout('Created.' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Error.' . PHP_EOL, Console::FG_RED);
        }
    }

    public function actionRestoreDump()
    {
        $config = [
            'lang' => [
                'ukr' => LOCALE_UK_UA,
                'rus' => LOCALE_RU_RU,
            ]
        ];

        if(Yii::$app->db->schema->getTableSchema('bread', true) == null) {
            $this->stdout('You have to restore your dump first.' . PHP_EOL, Console::FG_RED);
            $this->stdout('Run this command: cat dump.sql | docker exec -i ubfbread_db_1 /usr/bin/mysql -u app --password=app app' . PHP_EOL, Console::FG_YELLOW);
            exit();
        }

        $cmd = Yii::$app->db->createCommand()->delete('lessons');
        $this->stdout($cmd->rawSql . PHP_EOL, Console::FG_YELLOW);
        $cmd->execute();
        $total = (new Query())->from('bread')->count();
        $done = 0;
        Console::startProgress($done, $total, 'converting');

        $query = (new Query())->from('bread')->orderBy('id');
        foreach ($query->batch(25) as $rows) {
            Console::updateProgress($done, $total, 'preparing');
            $done+= count($rows);
            $lessons = array_map(function($row) use ($config) {
                return array_values([
                    'lang' => $config['lang'][$row['lang']] ?? 0,
                    'date' => $row['year']
                        . '-' . str_pad($row['month'], 2, "0", STR_PAD_LEFT)
                        . '-' . str_pad($row['date'], 2, "0", STR_PAD_LEFT),
                    'title' => $row['title'],
                    'passage_json' => Json::encode([
                        $row['book'],
                        $row['chapter'] ?: '', $row['verse'] ?: '',
                        $row['to_chapter'] ?: '', $row['to_verse'] ?: '',
                    ]),
                    'content_json' => Json::encode([
                        'a' => $row['keyverse'],
                        'b' => $row['text'],
                        'c' => $row['prayer'],
                        'd' => $row['one_word'],
                        'v' => 1,
                    ]),
                    'is_intro' => $row['is_intro'],
                    'deleted_at' => $row['is_deleted'] == 1 ? 1 : null,
                ]);
            }, $rows);
            Yii::$app->db->createCommand()
                ->batchInsert('lessons', ['lang', 'date', 'title', 'passage_json', 'content_json', 'is_intro', 'deleted_at'], $lessons)
                ->execute();
            Console::updateProgress($done, $total, 'inserted');
        }

        $this->stdout('Second step...' . PHP_EOL);

        $total = (new Query())->from('lessons')->count();
        //$total-= (new Query())->from((new Query())->select('date, is_intro')->from('lessons')->groupBy('date, is_intro')->having('count(*) > 1'))->count();
        $done = 0;
        Console::startProgress($done, $total, 'processing');

        function getNextLesson($id = null) {
            return (new Query())->from('lessons')
                ->where('parent_id is null')
                /*->andWhere([
                    'date' => (new Query())
                        ->select('date')
                        ->from('lessons')
                        ->groupBy('date')
                        ->having('count(*) > 1')
                        ->andFilterWhere(['>', 'id', $id])
                ])*/
                ->andFilterWhere(['>', 'id', $id])
                ->orderBy('id')
                ->one();
        }

        $lastId = null;
        while ($lesson = getNextLesson($lastId)) {
            $lastId = $lesson['id'];

            $done+= 1;
            $done+= Yii::$app->db->createCommand()->update('lessons', ['parent_id' => $lesson['id']], [
                'and',
                ['date' => $lesson['date']],
                ['is_intro' => $lesson['is_intro']],
                ['<>', 'id', $lesson['id']]
            ])->execute();

            Console::updateProgress($done, $total, 'processing');
        }

        $this->stdout('Third step...' . PHP_EOL);

        $data = Yii::$app->db
            ->createCommand('select lang, chapter as name from (select lang, JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons where deleted_at is null) as chapters group by lang, chapter')
            ->queryAll();

        Yii::$app->db->createCommand()->delete('categories')->execute();
        Yii::$app->db->createCommand()->batchInsert('categories', [
            'type', 'lang', 'name', 'alt'
        ], array_map(function($row) {
            return [1, $row['lang'], $row['name'], 0];
        }, array_filter($data, function($row) {
            return !empty($row['name']);
        })))->execute();

        $chapters = (new Query())->from('categories')->where(['type' => 1, 'alt' => 0])->all();
        $total = count($chapters);
        $done = 0;
        Console::startProgress($done, $total, 'chapters');
        foreach ($chapters as $chapter) {
            Console::updateProgress(++$done, $total, $chapter['name']);

            $data = Yii::$app->db
                ->createCommand('select id from (select id, JSON_UNQUOTE(JSON_EXTRACT(passage_json, \'$[0]\')) as chapter from lessons) as chapters where chapter=:chapter')
                ->bindParam(':chapter', $chapter['name'])
                ->queryAll();

            Yii::$app->db->createCommand()->update('lessons', [
                'chapter_id' => $chapter['id']
            ], [
                'id' => ArrayHelper::getColumn($data, 'id')
            ])->execute();
        }

        $this->stdout('Final step...' . PHP_EOL);

        $chapter2parent = (new Query())->select('chapter_id, parent_chapter_id')
            ->from([
                'a' => Lesson::find()->asArray()->alias('t')
                    ->select('t.chapter_id, p.chapter_id as parent_chapter_id, t.parent_id')
                    ->innerJoinWith('parent p', false)
                    ->parentIsNotNull('t')
            ])
            ->groupBy('chapter_id, parent_chapter_id')
            ->all();

        foreach ($chapter2parent as $row) {
            Yii::$app->db->createCommand()->update('categories', [
                'parent_id' => $row['parent_chapter_id']
            ], [
                'id' => $row['chapter_id'],
            ])->execute();
        }


        if($this->confirm('Do you want to remove table "bread"?')) {
            $cmd = Yii::$app->db->createCommand()->dropTable('bread');
            $this->stdout($cmd->rawSql . PHP_EOL, Console::FG_YELLOW);
            $cmd->execute();
        }

        $this->stdout('Done.' . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * Truncates all tables in the database.
     * @throws \yii\db\Exception
     */
    public function actionTruncate()
    {
        $dbName = Yii::$app->db->createCommand('SELECT DATABASE()')->queryScalar();
        if ($this->confirm('This will truncate all tables of current database [' . $dbName . '].')) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $tables = Yii::$app->db->schema->getTableNames();
            foreach ($tables as $table) {
                $this->stdout('Truncating table ' . $table . PHP_EOL, Console::FG_RED);
                Yii::$app->db->createCommand()->truncateTable($table)->execute();
            }
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
        }
    }

    /**
     * Drops all tables in the database.
     * @throws \yii\db\Exception
     */
    public function actionDrop()
    {
        $dbName = Yii::$app->db->createCommand('SELECT DATABASE()')->queryScalar();
        if ($this->confirm('This will drop all tables of current database [' . $dbName . '].')) {
            Yii::$app->db->createCommand("SET foreign_key_checks = 0")->execute();
            $tables = Yii::$app->db->schema->getTableNames();
            foreach ($tables as $table) {
                $this->stdout('Dropping table ' . $table . PHP_EOL, Console::FG_RED);
                Yii::$app->db->createCommand()->dropTable($table)->execute();
            }
            Yii::$app->db->createCommand("SET foreign_key_checks = 1")->execute();
        }
    }

    /**
     * @param string $charset
     * @param string $collation
     * @throws \yii\base\ExitException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function actionAlterCharset($charset = 'utf8mb4', $collation = 'utf8mb4_unicode_ci')
    {
        if (Yii::$app->db->getDriverName() !== 'mysql') {
            Console::error('Only mysql is supported');
            Yii::$app->end(1);
        }

        if (!$this->confirm("Convert tables to character set {$charset}?")) {
            Yii::$app->end();
        }

        $tables = Yii::$app->db->getSchema()->getTableNames();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        foreach ($tables as $table) {
            $command = Yii::$app->db->createCommand("ALTER TABLE {$table} CONVERT TO CHARACTER SET :charset COLLATE :collation")->bindValues([
                ':charset' => $charset,
                ':collation' => $collation
            ]);
            $command->execute();
        }
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
        Console::output('All ok!');
    }


    /**
     * Adds write permissions
     */
    public function actionSetWritable()
    {
        $this->setWritable($this->writablePaths);
    }

    /**
     * Adds execute permissions
     */
    public function actionSetExecutable()
    {
        $this->setExecutable($this->executablePaths);
    }

    /**
     * @param $paths
     */
    private function setWritable($paths)
    {
        foreach ($paths as $writable) {
            $writable = Yii::getAlias($writable);
            Console::output("Setting writable: {$writable}");
            @chmod($writable, 0777);
        }
    }

    /**
     * @param $paths
     */
    private function setExecutable($paths)
    {
        foreach ($paths as $executable) {
            $executable = Yii::getAlias($executable);
            Console::output("Setting executable: {$executable}");
            @chmod($executable, 0755);
        }
    }

    /**
     * @param $paths
     */
    private function setKeys($paths)
    {
        foreach ($paths as $file) {
            $file = Yii::getAlias($file);
            Console::output("Generating keys in {$file}");
            $content = file_get_contents($file);
            $content = preg_replace_callback('/<generated_key>/', function () {
                $length = 32;
                $bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
                return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
            }, $content);
            file_put_contents($file, $content);
        }
    }

    public function actionCreateVapidKeys()
    {
        file_put_contents(Yii::getAlias('@app/runtime/vapid.keys'), Json::encode(VAPID::createVapidKeys()));
        echo "Dene!";
    }
}
