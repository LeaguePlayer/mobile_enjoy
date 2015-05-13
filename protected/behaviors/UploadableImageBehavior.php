<?php
/**
 * @property string $savePath путь к директории, в которой сохраняем файлы
 */
Yii::import('appext.EPhpThumb.*');
class UploadableImageBehavior extends CActiveRecordBehavior
{
    /**
     * @var string название атрибута, хранящего в себе имя файла и файл
     */
    public $attributeName='image';
    public $saveImage=true;
    /**
     * @var string алиас директории, куда будем сохранять файлы (убедись, что директория существует)
     */
    public $saveUrl='uploads';
    protected $thumbsUrl;
    public $versions = array();

    protected $absoluteSavePath;
    protected $absoluteThumbsPath;
    protected $absoluteRetinaPath;
    protected $absoluteiPhone6Path;
    protected $absoluteiPhone6plusPath;

    /**
     * @var array сценарии валидации к которым будут добавлены правила валидации
     * загрузки файлов
     */
    public $scenarios = array('insert','update');

    /**
     * @var string типы файлов, которые можно загружать (нужно для валидации)
     */
    public $fileTypes='jpg, png, jpeg, gif';

    public function events(){
        return array(
            'onBeforeSave' => 'beforeSave',
            'onBeforeDelete' => 'beforeDelete',
        ); 
    }


    /**
     * Шорткат для Yii::getPathOfAlias($this->savePathAlias).DIRECTORY_SEPARATOR.
     * Возвращает путь к директории, в которой будут сохраняться файлы.
     * @return string путь к директории, в которой сохраняем файлы
     */
    public function getAbsoluteSavePath() {
        
           
        if ( $this->absoluteSavePath === null ) {
            $directories = explode('/', $this->saveUrl);
            $path = Yii::getPathOfAlias('webroot');

            // SiteHelper::mpr($this->owner->id);die();
            foreach ($directories as $subdirectory) {
                if ( empty($subdirectory) ) continue;
                $path .= DIRECTORY_SEPARATOR.$subdirectory;
            }
            // $path .= DIRECTORY_SEPARATOR.strtolower(get_class($this->owner));
            if(get_class($this->owner) == 'Image')
                $path .= DIRECTORY_SEPARATOR.strtolower($this->owner->block_id);
// echo $path;die();
            if ( !@is_dir($path) ) {
                mkdir($path, 0777, true);
            }
            $this->absoluteSavePath = $path;
        }
        return $this->absoluteSavePath;
    }


    public function getAbsoluteRetinaPath(){
        if ( $this->absoluteRetinaPath === null ) {
            $path = $this->getAbsoluteSavePath();
            $path .= DIRECTORY_SEPARATOR.'retina';
            if ( !@is_dir($path) ) {
                mkdir($path, 0777, true);
            }
            $this->absoluteRetinaPath = $path;
        }
        return $this->absoluteRetinaPath;
    }

    public function getAbsoluteiPhone6plusPath(){
        if ( $this->absoluteiPhone6plusPath === null ) {
            $path = $this->getAbsoluteSavePath();
            $path .= DIRECTORY_SEPARATOR.'iphone6plus';
            if ( !@is_dir($path) ) {
                mkdir($path, 0777, true);
            }
            $this->absoluteiPhone6plusPath = $path;
        }
        return $this->absoluteiPhone6plusPath;
    }

    public function getAbsoluteiPhone6Path(){
        if ( $this->absoluteiPhone6Path === null ) {
            $path = $this->getAbsoluteSavePath();
            $path .= DIRECTORY_SEPARATOR.'iphone6';
            if ( !@is_dir($path) ) {
                mkdir($path, 0777, true);
            }
            $this->absoluteiPhone6Path = $path;
        }
        return $this->absoluteiPhone6Path;
    }

     public function getAbsoluteThumbsPath(){
        // var_dump($this->getAbsoluteSavePath());
        
        if ( $this->absoluteThumbsPath === null ) {
            $path = $this->getAbsoluteSavePath();
            // echo $path;die();
            $path .= DIRECTORY_SEPARATOR.'thumbs';
            if ( !@is_dir($path) ) {
                mkdir($path, 0777, true);
            }
            $this->absoluteThumbsPath = $path;
            // echo $path;die();
        }
        return $this->absoluteThumbsPath;
    }

    public function getThumbsUrl()
    {
        if ( $this->thumbsUrl === null ) {
            $this->thumbsUrl = $this->saveUrl.'/'.strtolower(get_class($this->owner)).'/thumbs';
        }
        return $this->thumbsUrl;
    }

    public function attach($owner){
        parent::attach($owner);
        if(in_array($owner->scenario,$this->scenarios)){
            // добавляем валидатор файла
            $fileValidator=CValidator::createValidator('file',$owner,$this->attributeName,
                array('types'=>$this->fileTypes,'allowEmpty'=>true,'safe'=>false));
            $owner->validatorList->add($fileValidator);
        }
    }

    // имейте ввиду, что методы-обработчики событий в поведениях должны иметь
    // public-доступ начиная с 1.1.13RC
    public function beforeSave($event){
        // parent::afterSave($event);
        if ($this->saveImage)
        {
            if(in_array($this->owner->scenario,$this->scenarios) &&
                ($file=CUploadedFile::getInstance($this->owner,$this->attributeName))){
                $this->processDelete(); // старые файлы удалим, потому что загружаем новый

                $fileName = SiteHelper::genUniqueKey().'.'.$file->extensionName;
                $this->owner->setAttribute($this->attributeName,$fileName);
                $file->saveAs($this->getAbsoluteSavePath().DIRECTORY_SEPARATOR.$fileName);
                $this->createRetina($this->getAbsoluteSavePath(), $fileName);
                $this->createiPhone6($this->getAbsoluteSavePath(), $fileName);
                $this->createiPhone6plus($this->getAbsoluteSavePath(), $fileName);

                
                $this->createThumb($this->getAbsoluteSavePath(), $fileName);

                $this->replaceOriginal($this->getAbsoluteSavePath(), $fileName);
            }
        }
        return true;
    }

    protected function createiPhone6plus($filePath, $fileName)
    {
        $thumbsPath = $this->getAbsoluteiPhone6plusPath();


        $thumb = new EPhpThumb();
        $thumb->init();
        foreach ($this->versions as $version => $actions) {
            if($version!='iphone6plus') continue;
            $image = $thumb->create($filePath.DIRECTORY_SEPARATOR.$fileName);
            foreach ($actions as $method => $args) {
                call_user_func_array(array($image, $method), is_array($args) ? $args : array($args));
            }
            $image->save($thumbsPath.DIRECTORY_SEPARATOR.$fileName);
        }
    }

    protected function createiPhone6($filePath, $fileName)
    {
        $thumbsPath = $this->getAbsoluteiPhone6Path();
        $thumb = new EPhpThumb();
        $thumb->init();
        foreach ($this->versions as $version => $actions) {
            if($version!='iphone6') continue;
            $image = $thumb->create($filePath.DIRECTORY_SEPARATOR.$fileName);
            foreach ($actions as $method => $args) {
                call_user_func_array(array($image, $method), is_array($args) ? $args : array($args));
            }
            $image->save($thumbsPath.DIRECTORY_SEPARATOR.$fileName);
        }
    }

    protected function createThumb($filePath, $fileName)
    {
        $thumbsPath = $this->getAbsoluteThumbsPath();
        $thumb = new EPhpThumb();
        $thumb->init();
        foreach ($this->versions as $version => $actions) {
            if($version!='thumbs') continue;
            $image = $thumb->create($filePath.DIRECTORY_SEPARATOR.$fileName);
            foreach ($actions as $method => $args) {
                call_user_func_array(array($image, $method), is_array($args) ? $args : array($args));
            }
            $image->save($thumbsPath.DIRECTORY_SEPARATOR.$fileName);
        }
        
    }


    protected function replaceOriginal($filePath, $fileName)
    {
        $thumbsPath = $this->getAbsoluteSavePath();
        $thumb = new EPhpThumb();
        $thumb->init();

        foreach ($this->versions as $version => $actions) {
            if($version!='original') continue;
            $image = $thumb->create($filePath.DIRECTORY_SEPARATOR.$fileName);
            foreach ($actions as $method => $args) {
                call_user_func_array(array($image, $method), is_array($args) ? $args : array($args));
            }
            $image->save($thumbsPath.DIRECTORY_SEPARATOR.$fileName);
            // echo 'saved';
            // $image->save($thumbsPath.DIRECTORY_SEPARATOR.$version.'_'.$fileName);
        }
        // die();
    }

    // имейте ввиду, что методы-обработчики событий в поведениях должны иметь
    // public-доступ начиная с 1.1.13RC
    public function beforeDelete($event){
        $this->processDelete();
        return true;
    }

    protected function processDelete()
    {
        $this->deleteThumbs();
        $this->deleteFile(); // удалили модель? удаляем и файл, связанный с ней
    }

    public function deleteFile() {
        $filePath=$this->getAbsoluteSavePath().DIRECTORY_SEPARATOR.$this->owner->getAttribute($this->attributeName);
        if(@is_file($filePath))
            @unlink($filePath);
    }

    public function deleteThumbs()
    {
        $thumbsPath = $this->getAbsoluteRetinaPath();
        $fileName = $this->owner->getAttribute($this->attributeName);
        foreach ($this->versions as $version => $actions) {
            $thumbFile = $thumbsPath.DIRECTORY_SEPARATOR.$fileName;
            // $thumbFile = $thumbsPath.DIRECTORY_SEPARATOR.$version.'_'.$fileName;
            if(@is_file($thumbFile))
                @unlink($thumbFile);
        }
    }

    public function deletePhoto()
    {
        $this->processDelete();
        $this->owner->{$this->attributeName} = '';
        $this->owner->save(false);
    }

    protected function createRetina($filePath, $fileName)
    {
        $thumbsPath = $this->getAbsoluteRetinaPath();
        $thumb = new EPhpThumb();
        $thumb->init();

        
        foreach ($this->versions as $version => $actions) {
            if($version!='retina') continue;
            $image = $thumb->create($filePath.DIRECTORY_SEPARATOR.$fileName);
            foreach ($actions as $method => $args) {
                call_user_func_array(array($image, $method), is_array($args) ? $args : array($args));
            }
            $image->save($thumbsPath.DIRECTORY_SEPARATOR.$fileName);
            // echo 'saved';
            // $image->save($thumbsPath.DIRECTORY_SEPARATOR.$version.'_'.$fileName);
        }
        // die();
    }

    public function getImage($version = false, $alt = '', $htmlOptions = array())
    {
        $src = $this->getImageUrl($version);
        if ( class_exists('TbHtml') ) {
            return TbHtml::image($src, $alt, $htmlOptions);
        } else {
            return CHtml::image($src, $alt, $htmlOptions);
        }
    }

    public function getImageUrl($version = false)
    {
        if ($version) {
            return '/'.$this->getThumbsUrl().'/'.$version.'_'.$this->owner->getAttribute($this->attributeName);
        } else {
            return '/'.$this->saveUrl.'/'.$this->owner->getAttribute($this->attributeName);
        }
    }
}
