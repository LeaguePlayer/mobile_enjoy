<?php
/* @var $this AssignmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments'),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>
<ul id="yw2" class="nav nav-pills" role="menu">
    <li class="active" role="menuitem">
        <a tabindex="-1" href="/auth/assignment/index">Соответствия</a>
    </li>
    <li role="menuitem">
        <a tabindex="-1" href="/auth/role/index">Роли</a>
    </li>
    <li role="menuitem">
        <a tabindex="-1" href="/auth/task/index">Задания</a>
    </li>
    <li role="menuitem">
        <a tabindex="-1" href="/auth/operation/index">Операции</a>
    </li>
</ul>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped hover',
    'dataProvider' => $dataProvider,
	'emptyText' => Yii::t('AuthModule.main', 'No assignments found.'),
	'template'=>"{items}\n{pager}",
    'columns' => array(
        array(
            'header' => Yii::t('AuthModule.main', 'User'),
            'class' => 'AuthAssignmentNameColumn',
        ),
        array(
            'header' => Yii::t('AuthModule.main', 'Assigned items'),
            'class' => 'AuthAssignmentItemsColumn',
        ),

        array(
            'header'=>'Действия',
            'class' => 'AuthAssignmentRevokeColumn',
        ),
    ),
)); ?>
