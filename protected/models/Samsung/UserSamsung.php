<?php

/**
 * This is the model class for table "user_samsung".
 *
 * The followings are the available columns in table 'user_samsung':
 * @property string $userid
 * @property string $userid_prefix
 * @property integer $usertype
 * @property string $password
 * @property string $imei
 * @property string $vouchercode
 * @property string $created_date
 * @property string $modified_date
 */
class UserSamsung extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_samsung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid_prefix, password, imei, vouchercode', 'required'),
			array('usertype', 'numerical', 'integerOnly'=>true),
			array('userid', 'length', 'max'=>20),
			array('userid_prefix, imei, vouchercode', 'length', 'max'=>100),
			array('password', 'length', 'max'=>200),
			array('created_date, modified_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('userid, userid_prefix, usertype, password, imei, vouchercode, created_date, modified_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userid' => 'Userid',
			'userid_prefix' => 'Userid Prefix',
			'usertype' => 'Usertype',
			'password' => 'Password',
			'imei' => 'Imei',
			'vouchercode' => 'Vouchercode',
			'created_date' => 'Created Date',
			'modified_date' => 'Modified Date',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('userid_prefix',$this->userid_prefix,true);
		$criteria->compare('usertype',$this->usertype);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('imei',$this->imei,true);
		$criteria->compare('vouchercode',$this->vouchercode,true);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('modified_date',$this->modified_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserSamsung the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        
}
