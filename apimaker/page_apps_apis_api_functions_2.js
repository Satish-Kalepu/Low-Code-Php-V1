
var s2_snoitcnuf_gifnoc = {
	"Round": {
		"t": "Number",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Number", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "N"],},
			"p2": {"n": "Decimals", "t": "N", "v": "0", "m": true, "types": ["N"],},
			"p3": {"n": "Mode", "t": "RoundMode", "v": "", "m": false, "types": ["RoundMode"],},
		}
	},
	"Random Number": {
		"t": "Number",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Start", "t": "N", "v": 0, "m": true, "types": ["N"],},				
			"p2": {"n": "End", "t": "N", "v": 1000, "m": true, "types": ["N"],},
		}
	},
	"Number Format": {
		"t": "Number",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Number", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"],}, 
			"p2": {"n": "Decimals", "t": "N", "v": "0", "m": true, "types": ["N"],}, 
			"p3": {"n": "Format", "t": "NumberFormat", "v": "0", "m": true, "types": ["NumberFormat"],}
		}
	},
	"Text Padding": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Source", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V","T", "N"],}, 
			"p2": {"n": "Padding", "t": "N", "v": "0", "m": true, "types": ["N"],}, 
			"p3": {"n": "Symbol", "t": "T", "v": "0", "m": true, "types": ["T"],}, 
			"p4": {"n": "Mode", "t": "PaddingModes", "v": "", "m": true, "types": ["PaddingModes"],}
		}
	},
	"JSON Encode": {
		"t": "Convert",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Assoc List", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"],},
			"p2": {"n": "Pretty", "t": "B", "v": "false", "m": true, "types": ["B"],},
		}
	},
	"JSON Decode": {
		"t": "Convert",
		"self": false,
		"return": "O",
		"inputs": {
			"p1": {"n": "Json Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"],},
		}
	},
	"Text to Number": {
		"t": "Convert",
		"self": false,
		"return": "N",
		"inputs": {
		"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"],},
		}
	},
	"Number to Text": {
		"t": "Convert",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Number", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"],},
		}
	},
	"is it Text": {
		"t": "Convert",
		"self": false,
		"return": "B",
		"inputs": {
			"p1": {"n": "Text", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"is it Numeric": {
		"t": "Convert",
		"self": false,
		"return": "B",
		"inputs": {
			"p1": {"n": "Text", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"is it Binary": {
		"t": "Convert",
		"self": false,
		"return": "B",
		"inputs": {
			"p1": {"n": "Text", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Change Type": {
		"t": "Convert",
		"self": true,
		"return": false,
		"inputs": {
			"p1": {"n": "Variable", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Type", "types": ["BasicTypes"], "t": "BasicTypes", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Concat": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Text 1", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		"p2": {"n": "Text 2", "t": "T", "v": "", "m": true, "types": ["V", "T"],},
		"p3": {"n": "Text 3", "t": "T", "v": "", "types": ["V", "T"],},
		"p4": {"n": "Text 4", "t": "T", "v": "", "types": ["V", "T"],},
		}
	},
	"Sub String": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		"p2": {"n": "Start Index", "t": "N", "v": "0", "m": true, "types": ["V", "N"],},
		"p3": {"n": "Length", "t": "N", "v": "5", "m": true, "types": ["V", "N"],},
		}
	},
	"Number to Words": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"Split": {
		"t": "Text",
		"self": false,
		"return": "L",
		"returnd": [{"t":"T", "v":""}],
		"inputs": {
			"p1": {"n": "Source Text", "types": ["V", "T"], "t": "T", "v": "", "m": true,},
			"p2": {"n": "Delimeter", "types": ["V", "T"], "t": "T", "v": "", "m": true,},
			"p3": {"n": "Limit", "types": ["N"], "t": "N", "v": "-1", "m": true,},
		}
	},
	"Replace Text": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Source Text", "types": ["V", "T"], "t": "T", "v": "", "m": true,},
		"p2": {"n": "Find Word", "types": ["V", "T"], "t": "T", "v": "", "m": true,},
		"p3": {"n": "Replace With", "types": ["V", "T"], "t": "T", "v": "", "m": true,},
		}
	},
	"to Upper Case": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"to Lower Case": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
		"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"Trim": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"UniqID": {
		"self": false,
		"return": "T",
	},
	"MongoDBId": {
		"t": "Text",
		"self": false,
		"return": "T",
	},
	"Clean Text": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"Match Pattern": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "T", "v": "1983-03-01", "m": true, "types": ["V", "T"],},
			"p2": {"n": "Pattern", "t": "T", "v": "/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/i", "m": true, "types": ["V", "T"],},
			"p3": {"n": "If Matched", "t": "MatchReturn", "v": "true", "m": true, "types": ["MatchReturn"],},
		}
	},
	"Validate Input": {
		"t": "Text",
		"self": false,
		"return": "B",
		"inputs": {
			"p1": {"n": "Variable", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V"]},
			"p2": {"n": "Validation", "t": "Validation", "v": "Email", "m": true, "types": ["Validation"]},
		}
	},
	"HTML Entity Decode": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "HTML/XML Text", "t": "V", "v": {"v":"","t":""}, "m": true, "types": ["V", "T"],},
		}
	},
	"Url Encode": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "T", "v": "", "m": true, "types": ["V", "T"],},
		}
	},
	"Url Decode": {
		"t": "Text",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text", "t": "T", "v": "", "m": true, "types": ["V", "T"],},
		}
	},
	"Get Date String": {
		"t": "Date",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Date Format", "types": ["T"], "t": "T", "m": true, "v": "Y-m-d H:i:s", "h":"date.html"},
			"p2": {"n": "Timestamp", "types": ["TS", "N", "NL"], "t": "TS", "m": false, "v": "-1", "hh":"Optional Parameter<BR>NL or -1 to ignore"},
		}
	},
	"Get Date": {
		"t": "Date",
		"self": false,
		"return": "D",
	},
	"Get DateTime": {
		"t": "Date",
		"self": false,
		"return": "DT",
	},
	"Get Timestamp": {
		"t": "Date",
		"self": false,
		"return": "TS",
	},
	"Set Timezone": {
		"t": "Date",
		"self": true,
		"return": false,
		"inputs": {
			"p1": {"n": "Timezone", "types": ["TimeZone"], "t": "TimeZone", "m": true, "v": "UTC"},
		}
	},
	"Get Timezone": {
		"t": "Date",
		"self": false,
		"return": "T",
	},
	"Date To Timestamp": {
		"t": "Date",
		"self": false,
		"return": "TS",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Timestamp To Date String": {
		"t": "Date",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Timestamp", "types": ["V", "T", "TS", "N"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Date Format", "types": ["T"], "t": "T", "m": true, "v": "Y-m-d H:i:s", "h":"date.html"},
		}
	},
	"Timestamp To Date": {
		"t": "Date",
		"self": false,
		"return": "D",
		"inputs": {
			"p1": {"n": "Timestamp", "types": ["V", "T", "TS", "N"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Timestamp To DateTime": {
		"t": "Date",
		"self": false,
		"return": "TS",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "T", "TS", "N"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Years till Today": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Months till Today": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Days till Today": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Days Diff": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date 1", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Date 2", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Months Diff": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date 1", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Date 2", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Years Diff": {
		"t": "Date",
		"self": false,
		"return": "N",
		"inputs": {
			"p1": {"n": "Date 1", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Date 2", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Add Days": {
		"t": "Date",
		"self": false,
		"return": "D",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "No of Days", "types": ["V", "N"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Minus Days": {
		"t": "Date",
		"self": false,
		"return": "D",
		"inputs": {
			"p1": {"n": "Date", "types": ["V", "date"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "No of Days", "types": ["V", "N"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Change Format": {
		"t": "Date",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Date String", "types": ["V", "date"], "t": "T", "m": true, "v": "","init":"today"},
			"p2": {"n": "Change to Format", "types": ["DateFormat"], "t": "DateFormat", "m": true, "v": "",},
		}
	},
	"List Length": {
		"t"	: "List",
		"self": false,
		"return": "N",
		"inputs": {
			"p1"	: {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""},},
		}
	}, 
	"Get List Item": {
		"t": "List",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Index", "types": ["V", "N"], "t": "N", "m": true, "v": "0",},
		}
	},
	"List Append": {
		"t": "List",
		"self": false,
		"return": false,
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Item", "types": ["V", "T", "N", "O"], "t": "T", "m": true, "v": "",},
		}
	},
	"List Prepend": {
		"t": "List",
		"self": true,
		"return": false,
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Item", "types": ["V", "T", "N","O"], "t": "T", "m": true, "v": "",},
		}
	},
	"List Item Remove": {
		"t": "List",
		"self": false,
		"return": "O",
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Index", "types": ["V", "N"], "t": "N", "m": true, "v": "0",},
		}
	},
	"Get Value": {
		"t": "Assoc List",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""},},
			"p2": {"n": "Key", "types": ["V", "T"], "t": "T", "m": true, "v": "",},
		}
	},
	"Set Value": {
		"t": "Assoc List",
		"self": true,
		"return": false,
		"inputs": {
			"p1": {"n": "Data", "types": ["V"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Key", "types": ["V", "T"], "t": "T", "m": true, "v": "",},
			"p3": {"n": "Value", "types": ["V", "T", "N"], "t": "T", "m": true, "v": "",},
		}
	},
	"Base64 Encode": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Text", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Base64 Decode": {
		"t": "Cryptography",
		"self": false,
		"return": "BIN",
		"inputs": {
			"p1": {"n": "Text", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
		}
	},
	"Generate IV": {
		"t": "Cryptography",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Length", "types": ["V", "N"], "t": "N", "m": true, "v": 16,},
			"p2": {"n": "Type", "types": ["IVType"], "t": "IVType", "m": true, "v": "NULL Bytes"},
		}
	},
	"Hash": {
		"t": "Cryptography",
		"self": false,
		"return": "T",
		"inputs": {
			"p1": {"n": "Text to Hash", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Algorithem", "types": ["V", "HashAlgorithem"], "t": "HashAlgorithem", "m": true, "v": "md5",},
			"p3": {"n": "Salt", "types": ["T"], "t": "T", "m": false, "v": "", "encrypt": true},
		}
	},
	"OpenSSL Encrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Text", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Algorithem", "types": ["V", "Algorithem"], "t": "Algorithem", "m": true, "v": "AES128",},
			"p3": {"n": "Key", "types": ["V", "T"], "t": "T", "m": true, "v": "", "encrypt": true},
			"p4": {"n": "IV", "types": ["NL","IVType", "V", "T"], "t": "NL", "m": false, "v": "",},
		}
	},
	"OpenSSL Decrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Encrypted", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "Algorithem", "types": ["V", "Algorithem"], "t": "Algorithem", "m": true, "v": "AES128",},
			"p3": {"n": "Key", "types": ["V", "T"], "t": "T", "m": true, "v": "", "encrypt": true},
			"p4": {"n": "IV", "types": ["NL","IVType", "V", "T"], "t": "NL", "m": false, "v": "",},
		}
	},
	"OpenSSL Public Encrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Text", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "RSA Public Key", "types": ["TT", "V"], "t": "TT", "m": true, "v": "", "encrypt": true},
			"p3": {"n": "Padding", "types": ["Padding"], "t": "Padding", "m": true, "v": "OPENSSL_NO_PADDING",},
			"p4": {"n": "Base64Encode", "types": ["B"], "t": "B", "m": true, "v": true },
		}
	},
	"OpenSSL Public Decrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Encrypted", "types": ["V", "T", "B64"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "RSA Public Key", "types": ["TT", "V"], "t": "TT", "m": true, "v": "", "encrypt": true},
			"p3": {"n": "Padding", "types": ["Padding"], "t": "Padding", "m": true, "v": "OPENSSL_NO_PADDING",},
		}
	},
	"OpenSSL Private Encrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Text", "types": ["V", "T"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "RSA Private Key", "types": ["TT", "V"], "t": "TT", "m": true, "v": "", "encrypt": true},
			"p3": {"n": "Padding", "types": ["Padding2"], "t": "Padding2", "m": true, "v": "OPENSSL_NO_PADDING",},
			"p4": {"n": "Base64Encode", "types": ["B"], "t": "B", "m": true, "v": true },
		}
	},
	"OpenSSL Private Decrypt": {
		"t": "Cryptography",
		"self": false,
		"return": "B64",
		"inputs": {
			"p1": {"n": "Encrypted", "types": ["V", "T", "TT", "B64"], "t": "V", "m": true, "v": {"v":"","t":""}},
			"p2": {"n": "RSA Private Key", "types": ["TT", "V"], "t": "TT", "m": true, "v": "", "encrypt": true },
			"p3": {"n": "Padding", "types": ["Padding"], "t": "Padding", "m": true, "v": "OPENSSL_NO_PADDING",},
		}
	},
};



var s2_atad_snoitcnuf_gifnoc = {
	"NumberFormat": [
		"Universal",
		"UK",
		"USA",
		"Indian"
	],
	"PaddingModes": ["Left","Right","Both"],
	"IVType": ["NullBytes", "RandomText"],
	"Algorithem": [
		"AES128",
		"AES192",
		"AES256",
		"IDEA",
		"BLOWFISH",
		"DES",
		"AES-128-CBC",
		"AES-128-CFB",
		"AES-128-CFB1",
		"AES-128-CFB8",
		"AES-128-ECB",
		"AES-128-OFB",
		"AES-192-CBC",
		"AES-192-CFB",
		"AES-192-CFB1",
		"AES-192-CFB8",
		"AES-192-ECB",
		"AES-192-OFB",
		"AES-256-CBC",
		"AES-256-CFB",
		"AES-256-CFB1",
		"AES-256-CFB8",
		"AES-256-ECB",
		"AES-256-OFB"
	],
	"HashAlgorithem": [
		"md2",
		"md4",
		"md5",
		"sha1",
		"sha224",
		"sha256",
		"sha384",
		"sha512/224",
		"sha512/256",
		"sha512",
		"sha3-224",
		"sha3-256",
		"sha3-384",
		"sha3-512",
		"ripemd128",
		"ripemd160",
		"ripemd256",
		"ripemd320",
		"whirlpool",
		"tiger128,3",
		"tiger160,3",
		"tiger192,3",
		"tiger128,4",
		"tiger160,4",
		"tiger192,4",
		"snefru",
		"snefru256",
		"gost",
		"gost-crypto",
		"adler32",
		"crc32",
		"crc32b",
		"fnv132",
		"fnv1a32",
		"fnv164",
		"fnv1a64",
		"joaat",
		"haval128,3",
		"haval160,3",
		"haval192,3",
		"haval224,3",
		"haval256,3",
		"haval128,4",
		"haval160,4",
		"haval192,4",
		"haval224,4",
		"haval256,4",
		"haval128,5",
		"haval160,5",
		"haval192,5",
		"haval224,5",
		"haval256,5"
	],
	"Padding": [
		"OPENSSL_PKCS1_PADDING",
		"OPENSSL_SSLV23_PADDING",
		"OPENSSL_PKCS1_OAEP_PADDING",
		"OPENSSL_NO_PADDING"
	],
	"Padding2": [
		"OPENSSL_PKCS1_PADDING",
		"OPENSSL_NO_PADDING"
	],
	"RoundMode": [
		"Default",
		"Upper",
		"Lower",  
	],
	"DateFormat": [
		"Y-m-d",
		"d-m-Y",
		"Y/m/d",
		"d/m/Y",
		"Y-M-d",
		"d-M-Y",
		"Y MM d",
		"d MM Y",
		"MM d, Y",
	],
	"DateTimeFormat": [
		"Y-m-d hh:mm:ss",
		"d-m-Y hh:mm:ss",
		"Y/m/d hh:mm",
		"d/m/Y hh:mm",
		"Y-M-d hh:mm",
		"d-M-Y hh:mm",
		"hh:mm:ss",
		"hh:mm",
	],
	"MatchReturn": [
		"True",
		"Matched String",
		"Matched Group 1",
		"Matched Group 2",
		"Matched Group 3"
	],
	"B": [
		"true",
		"false"
	],
	"SortOrder": ["a-z","z-a"],
	"BasicTypes": ["T", "N", "B", "O", "L", "BIN", "B64"],
	"TimeZone": ["UTC-12:00","UTC-11:00","UTC-10:00","UTC-09:30","UTC-09:00","UTC-08:00","UTC-07:00","UTC-06:00","UTC-05:00","UTC-04:00","UTC-03:30","UTC-03:00","UTC-02:00","UTC-01:00","UTC+00:00","UTC+01:00","UTC+02:00","UTC+03:00","UTC+03:30","UTC+04:00","UTC+04:30","UTC+05:00","UTC+05:30","UTC+05:45","UTC+06:00","UTC+06:30","UTC+07:00","UTC+08:00","UTC+08:45","UTC+09:00","UTC+09:30","UTC+10:00","UTC+10:30","UTC+11:00","UTC+12:00","UTC+12:45","UTC+13:00","UTC+14:00"],
	"HttpMethod": ["GET", "POST", "PUT", "OPTIONS", "HEAD"],
	"DFormat": ["yyyy-mm-dd","dd-mm-yyyy","dd/mm/yyyy","yyyy/mm/dd","mm/dd/yyyy", "yyyy/dd/mm", "yyyy-M-dd", "yyyy M dd", "dd-M-yyyy", "dd M yyyy", "yyyy-MM-dd", "yyyy MM dd", "dd-MM-yyyy", "dd MM yyyy", "dd DD MM yyyy", "yyyy DD dd MM"],
	"ContentType": ["application/json","application/x-www-form-urlencoded","application/xml", "text/plain", "multipart/form-data"],
	"mongooperator": ['$eq','$gt', '$gte', '$lt', '$lte', '$exists', '$ne', '$regex'],
	"Validation": [ "Mobile", "Phone", "Email", "Domain", "URL", "Alpha", "Alpha with spaces", "Alpha with space - . _", "AlphaNumeric", "AlphaNumeric with spaces", "AlphaNumeric with space - . _", "Numeric", "PAN", "Aadhaar", "Credit Card", "dd/mm/yyyy", "yyyy/dd/mm", "yyyy-mm-dd", "dd-mm-yyyy", "MongoDBId", "HexCode" ],
	"auth-type": ["None", "Access-Key", "Credentials", "Bearer"],
	"MongoOp": ['$eq','$lt','$lte','$gt','$gte','$exists','$or','$and'],
	"MysqlOp": ['=','!=','<','<=','>','>='],
};