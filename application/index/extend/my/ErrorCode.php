<?php
namespace my;
/**
 * error code 说明.
 * <ul>

 *    <li>-000000: 请求正常并返回数据</li>
 *    <li>-100000: 数据库操作失败</li>
 *    <li>-100001: 数据库系统错误</li>
 *    <li>-200000: 缺少请求参数</li>
 *    <li>-200001: 操作失败</li>
 *    <li>-200002: 重复操作</li>
 *    <li>-200003: 非法操作</li>
 *    <li>-300000: 获取微信登录session_key错误</li>
 *    <li>-300001: 微信登录签名失败</li>
 *    <li>-300002: 微信登录敏感信息解密失败</li>
 *    <li>-300003: 微信登录校验失败</li>
 *    <li>-400001: 登录失效</li>
 * </ul>
 */
class ErrorCode
{
	public static $SUCCESS = '000000';
	public static $DATABASE_SEARCH_ERROR = '100000';
	public static $DATABASE_SYS_ERROR = '100001';
	public static $MISSING_PARAMETER_ERROR = '200000';
	public static $HANDLE_ERROR = '200001';
	public static $REPETITION_HANDLE_ERROR = '200002';
	public static $ILLEGAL_HANDLE_ERROR = '200003';
	public static $GET_WX_LOGIN_SESSION_KEY_ERROR = '300000';
	public static $WX_LOGIN_SIGNATURE_ERROR = '300001';
	public static $WX_LOGIN_ENCRYPT_DATA_ERROR = '300002';
	public static $WX_LOGIN_VERIFY_ERROR = '300003';
	public static $LOGIN_VERIFY = '400001';
}

?>