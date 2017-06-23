<?php
namespace JT\JTOAuth;

/**
 * @author nvb <nvb@aproxima.ru>
 */
class JTOAuthUser
{
	/**
	 * @var integer
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $firstName;
	
	/**
	 * @var string
	 */
	protected $secondName;
	
	/**
	 * @var string
	 */
	protected $lastName;
	
	/**
	 * @var string
	 */
	protected $company;
	
	/**
	 * @var string
	 */
	protected $mail;
	
	/**
	 * @var string
	 */
	protected $phone;
	
	/**
	 * @var array
	 */
	protected $roles;
	
	/**
	 * @param integer $id
	 * @param string  $firstName
	 * @param string  $secondName
	 * @param string  $lastName
	 * @param string  $company
	 * @param string  $mail
	 * @param string  $phone
	 * @param array   $roles
	 */
	public function __construct(
		$id,
		$firstName,
		$secondName,
		$lastName,
		$company,
		$mail,
		$phone,
		array $roles
	)
	{
		$this->id = (int) $id;
		$this->firstName = $firstName;
		$this->secondName = $secondName;
		$this->lastName = $lastName;
		$this->company = $company;
		$this->mail = $mail;
		$this->phone = $phone;
		$this->roles = $roles;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * @return string
	 */
	public function getSecondName()
	{
		return $this->secondName;
	}
	
	/**
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}
	
	/**
	 * @return string
	 */
	public function getCompany()
	{
		return $this->company;
	}
	
	/**
	 * @return string
	 */
	public function getMail()
	{
		return $this->mail;
	}
	
	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @return roles
	 */
	public function getRoles()
	{
		return $this->roles;
	}
}