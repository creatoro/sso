<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Jelly auth user for saving users without required fields
 *
 * @package     Jelly/SSO
 * @author      creatoro
 * @copyright   (c) 2010 creatoro
 * @license     http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Model_User_SSO_Jelly extends Model_Auth_User
{
	public static function initialize(Jelly_Meta $meta)
    {
        $meta->table('users');

        // Fields defined by the model
        $meta->fields(array(
            'id' 	=> new Jelly_Field_Primary,
			'email' => new Jelly_Field_Email(array(
				'unique' => TRUE,
			)),
			'twitter_id' => new Jelly_Field_String(array(
				'unique' => TRUE,
				'default' => NULL,
			)),
			'facebook_id' => new Jelly_Field_Integer(array(
				'unique' => TRUE,
				'default' => NULL,
			)),

			// Relationships to other models
            'roles'  => new Jelly_Field_ManyToMany(array(
				'foreign' => 'roles.id',
    			'through' => array(
					'model' => 'roles_users',
    				'fields' => array('user:foreign_key', 'role:foreign_key'),
				),
			)),
        ));
    }

} // End Model_User_SSO_Jelly