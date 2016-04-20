<?php

/**
 * This is the model class for table "user_moviebay".
 *
 * The followings are the available columns in table 'user_moviebay':
 * @property string $email
 * @property string $userid
 * @property integer $usertype
 * @property string $password
 * @property string $name
 * @property string $dateofbirth
 * @property string $gender
 * @property integer $userstatus
 * @property string $phonenumber
 */
class UserMoviebay extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_moviebay';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email,usertype,userid','required'),
                        array('email','unique'),    
                        array('email','email'),
			array('usertype, userstatus', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>50),
			array('userid, gender, phonenumber', 'length', 'max'=>20),
			array('password', 'length', 'max'=>200),
			array('name', 'length', 'max'=>80),
			array('dateofbirth', 'safe'),
                        array('dateofbirth', 'type', 'type' => 'date', 'message' => '{attribute}: is not a valid date!', 'dateFormat' => 'yyyy-MM-dd'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('email, userid, usertype, password, name, dateofbirth, gender, userstatus, phonenumber', 'safe', 'on'=>'search'),
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
			'email' => 'Email',
			'userid' => 'Userid',
			'usertype' => 'Usertype',
			'password' => 'Password',
			'name' => 'Name',
			'dateofbirth' => 'Dateofbirth',
			'gender' => 'Gender',
			'userstatus' => 'Userstatus',
			'phonenumber' => 'Phonenumber',
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

		$criteria->compare('email',$this->email,true);
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('usertype',$this->usertype);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('dateofbirth',$this->dateofbirth,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('userstatus',$this->userstatus);
		$criteria->compare('phonenumber',$this->phonenumber,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserMoviebay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        
        
        
}
