<?php
/*************************************************************************************
 * csharp.php
 * ----------
 * Author: Alan Juden (alan@judenware.org)
 * Copyright: (c) 2004 Alan Juden, Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.7.14
 * CVS Revision Version: $Revision: 1.14.2.5 $
 * Date Started: 2004/06/04
 * Last Modified: $Date: 2006/08/22 10:42:34 $
 *
 * C# language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2005/01/05 (1.0.1)
 *  -  Used hardquote support for @"..." strings (Cliff Stanford)
 * 2004/11/27 (1.0.0)
 *  -  Initial release
 *
 * TODO (updated 2004/11/27)
 * -------------------------
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

 $language_data = array (
	'LANG_NAME' => 'C#',
	'COMMENT_SINGLE' => array(1 => '//', 2 => '#'),
	'COMMENT_MULTI' => array('/*' => '*/'),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
	'QUOTEMARKS' => array("'", '"'),
    'HARDQUOTE' => array('@"', '"'),
    'HARDESCAPE' => array('""'),
	'ESCAPE_CHAR' => '\\',
	'KEYWORDS' => array(
		1 => array(
			'as', 'auto', 'base', 'break', 'case', 'catch', 'const', 'continue',
			'default', 'do', 'else', 'event', 'explicit', 'extern', 'false',
			'finally', 'fixed', 'for', 'foreach', 'goto', 'if', 'implicit',
			'in', 'internal', 'lock', 'namespace', 'null', 'operator', 'out',
			'override', 'params', 'private', 'protected', 'public', 'readonly',
			'ref', 'return', 'sealed', 'stackalloc', 'static', 'switch', 'this',
			'throw', 'true', 'try', 'unsafe', 'using', 'virtual', 'void', 'while'
			),
		2 => array(
			'#elif', '#endif', '#endregion', '#else', '#error', '#define', '#if',
			'#line', '#region', '#undef', '#warning'
			),
		3 => array(
			'checked', 'is', 'new', 'sizeof', 'typeof', 'unchecked'
			),
		4 => array(
			'bool', 'byte', 'char', 'class', 'decimal', 'delegate', 'double',
			'enum', 'float', 'int', 'interface', 'long', 'object', 'sbyte',
			'short', 'string', 'struct', 'uint', 'ulong', 'ushort'
			),
		5 => array(
			'Microsoft.Win32',
			'System',
			'System.CodeDOM',
			'System.CodeDOM.Compiler',
			'System.Collections',
			'System.Collections.Bases',
			'System.ComponentModel',
			'System.ComponentModel.Design',
			'System.ComponentModel.Design.CodeModel',
			'System.Configuration',
			'System.Configuration.Assemblies',
			'System.Configuration.Core',
			'System.Configuration.Install',
			'System.Configuration.Interceptors',
			'System.Configuration.Schema',
			'System.Configuration.Web',
			'System.Core',
			'System.Data',
			'System.Data.ADO',
			'System.Data.Design',
			'System.Data.Internal',
			'System.Data.SQL',
			'System.Data.SQLTypes',
			'System.Data.XML',
			'System.Data.XML.DOM',
			'System.Data.XML.XPath',
			'System.Data.XML.XSLT',
			'System.Diagnostics',
			'System.Diagnostics.SymbolStore',
			'System.DirectoryServices',
			'System.Drawing',
			'System.Drawing.Design',
			'System.Drawing.Drawing2D',
			'System.Drawing.Imaging',
			'System.Drawing.Printing',
			'System.Drawing.Text',
			'System.Globalization',
			'System.IO',
			'System.IO.IsolatedStorage',
			'System.Messaging',
			'System.Net',
			'System.Net.Sockets',
			'System.NewXml',
			'System.NewXml.XPath',
			'System.NewXml.Xsl',
			'System.Reflection',
			'System.Reflection.Emit',
			'System.Resources',
			'System.Runtime.InteropServices',
			'System.Runtime.InteropServices.Expando',
			'System.Runtime.Remoting',
			'System.Runtime.Serialization',
			'System.Runtime.Serialization.Formatters',
			'System.Runtime.Serialization.Formatters.Binary',
			'System.Security',
			'System.Security.Cryptography',
			'System.Security.Cryptography.X509Certificates',
			'System.Security.Permissions',
			'System.Security.Policy',
			'System.Security.Principal',
			'System.ServiceProcess',
			'System.Text',
			'System.Text.RegularExpressions',
			'System.Threading',
			'System.Timers',
			'System.Web',
			'System.Web.Caching',
			'System.Web.Configuration',
			'System.Web.Security',
			'System.Web.Services',
			'System.Web.Services.Description',
			'System.Web.Services.Discovery',
			'System.Web.Services.Protocols',
			'System.Web.UI',
			'System.Web.UI.Design',
			'System.Web.UI.Design.WebControls',
			'System.Web.UI.Design.WebControls.ListControls',
			'System.Web.UI.HtmlControls',
			'System.Web.UI.WebControls',
			'System.WinForms',
			'System.WinForms.ComponentModel',
			'System.WinForms.Design',
			'System.Xml',
			'System.Xml.Serialization',
			'System.Xml.Serialization.Code',
			'System.Xml.Serialization.Schema'
			),
		),
	'SYMBOLS' => array(
		'+', '-', '*', '?', '=', '/', '%', '&', '>', '<', '^', '!', '|', ':',
		'(', ')', '{', '}', '[', ']'
		),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => true,
		1 => false,
		2 => false,
		3 => false,
		4 => false,
		5 => false,
		),
	'STYLES' => array(
		'KEYWORDS' => array(
			1 => 'color: #0600FF;',
			2 => 'color: #FF8000; font-weight: bold;',
			3 => 'color: #008000;',
			4 => 'color: #FF0000;',
			5 => 'color: #000000;'
			),
		'COMMENTS' => array(
			1 => 'color: #008080; font-style: italic;',
			2 => 'color: #008080;',
			'MULTI' => 'color: #008080; font-style: italic;'
			),
		'ESCAPE_CHAR' => array(
			0 => 'color: #008080; font-weight: bold;'
			),
		'BRACKETS' => array(
			0 => 'color: #000000;'
			),
		'STRINGS' => array(
			0 => 'color: #808080;'
			),
		'NUMBERS' => array(
			0 => 'color: #FF0000;'
			),
		'METHODS' => array(
			1 => 'color: #0000FF;',
			2 => 'color: #0000FF;'
			),
		'SYMBOLS' => array(
			0 => 'color: #008000;'
			),
		'REGEXPS' => array(
			),
		'SCRIPT' => array(
			)
		),
	'URLS' => array(
		1 => '',
		2 => '',
		3 => 'http://www.google.com/search?q={FNAME}+msdn.microsoft.com',
		4 => ''
		),
	'OOLANG' => true,
	'OBJECT_SPLITTERS' => array(
		1 => '.',
		2 => '::'
		),
	'REGEXPS' => array(
		),
	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(
		),
	'HIGHLIGHT_STRICT_BLOCK' => array(
		)
);

?>
