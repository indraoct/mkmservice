<?php

/**
 * This is the model class for table "iptv_services".
 *
 * The followings are the available columns in table 'iptv_services':
 * @property string $id
 * @property string $servicename
 * @property string $userid
 * @property string $msg_request
 * @property string $msg_response
 * @property string $log_date
 * @property string $request_time
 * @property string $response_time
 * @property string $ip_address
 * @property string $mac_address
 */
class IptvServices extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iptv_services';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id', 'length', 'max'=>20),
			array('servicename', 'length', 'max'=>45),
			array('userid, ip_address, mac_address', 'length', 'max'=>100),
			array('msg_request, msg_response', 'length', 'max'=>2000),
			array('log_date, request_time, response_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, servicename, userid, msg_request, msg_response, log_date, request_time, response_time, ip_address, mac_address', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'servicename' => 'Servicename',
			'userid' => 'Userid',
			'msg_request' => 'Msg Request',
			'msg_response' => 'Msg Response',
			'log_date' => 'Log Date',
			'request_time' => 'Request Time',
			'response_time' => 'Response Time',
			'ip_address' => 'Ip Address',
			'mac_address' => 'Mac Address',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('servicename',$this->servicename,true);
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('msg_request',$this->msg_request,true);
		$criteria->compare('msg_response',$this->msg_response,true);
		$criteria->compare('log_date',$this->log_date,true);
		$criteria->compare('request_time',$this->request_time,true);
		$criteria->compare('response_time',$this->response_time,true);
		$criteria->compare('ip_address',$this->ip_address,true);
		$criteria->compare('mac_address',$this->mac_address,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IptvServices the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        
}
