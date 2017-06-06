<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Admin;

/**
 * Adminsearch represents the model behind the search form about `common\models\Admin`.
 */
class Adminsearch extends Admin
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'department_id', 'role_id', 'last_login', 'status', 'is_first', 'is_safety'], 'integer'],
            [['username', 'password', 'auth_key', 'mobile', 'last_ip', 'name', 'id_card', 'job_number', 'entry_date', 'leave_date', 'create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Admin::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'department_id' => $this->department_id,
            'role_id' => $this->role_id,
            'last_login' => $this->last_login,
            'status' => $this->status,
            'entry_date' => $this->entry_date,
            'leave_date' => $this->leave_date,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'is_first' => $this->is_first,
            'is_safety' => $this->is_safety,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'last_ip', $this->last_ip])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'id_card', $this->id_card])
            ->andFilterWhere(['like', 'job_number', $this->job_number]);

        return $dataProvider;
    }
}
