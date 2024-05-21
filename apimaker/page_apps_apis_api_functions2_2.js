var s2_seitreporp_tcejbo_gifnoc = {
	"T": {
		"get": {"self":false,"return":"self"},
		"set": {"self":true,"return":"T","inputs":{
			"p2": {"n": "Value", "m": true, "types": ["V", "T"], "v": {"t":"T","v":""}},
		}},
		"toLowerCase": {"self": true, "replace": true, "return":"T"},
		"toUpperCase": {"self": true, "replace": true, "return":"T"},
		"toProperCase": {"self": true, "replace": true, "return":"T"},
		"trim": {"self": true, "replace": true, "return":"T"},
		"matchPattern": {"self": false,"return":"T","inputs":{
			"p2": {"n": "Pattern", "m": true, "types": ["V", "T"], "v": {"t":"T","v":"/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/i"} },
			"p3": {"n": "If Matched", "m": true, "types": ["match_return"], "v": {"t":"match_return","v":"true"} },
		}},
		"searchPattern": {"self": false,"return":"L","inputs":{
			"p2": {"n": "Pattern", "m": true, "types": ["V", "T"], "v": {"t":"T","v":"/([a-z0-9]+)/i"} }
		}},
		"validate": {"self": false,"return":"B","inputs":{
			"p2": {"n": "Rule", "m": true, "types": ["Validation"], "v": {"t":"Validation", "v":"Email"} },
			"p3": {"n": "MinLength", "m": true, "types": ["N"], "v": {"t":"N", "v":"2"} },
			"p4": {"n": "MaxLength", "m": true, "types": ["N"], "v": {"t":"N", "v":"50"} },
		}},
		"isNumeric": {"self": false,"return":"T"},
		"subString": {"self": true, "replace": true, "return":"T", "inputs":{
			"p2": {"n": "Start Index", "m": true, "types": ["V", "N"], "v": {"t":"N","v":"0"}},
			"p3": {"n": "Length", "m": true, "types": ["V", "N"], "v": {"t":"N","v":"5"}},
		}},
		"length": {"self": false,"return":"N"},
		"append": {"self": true, "replace": true,  "return":"B", "inputs":{
			"p2": {"n": "Text", "m": true, "types": ["V", "T"], "v": {"t":"T","v":"Something"}},
		}},
		"prepend": {"self": true, "replace": true, "return":"T", "inputs":{
			"p2": {"n": "Text", "m": true, "types": ["V", "T"], "v": {"t":"T","v":"Something"}},
		}},
		"clean": {"self": true, "replace": true, "return":"T"},
		"convertToNumber": {"self": false,"return":"N"},
		"split": {"self": false,"return":"L", "inputs": {
			"p2": {"n": "Pattern", "types": ["V", "T"], "m": true, "v": {"t":"T","v":":"}},
			"p3": {"n": "Limit", "types": ["N"], "m": false, "v": {"t":"N","v":"-1"}},
		}},
		"replace": {"self": true, "replace": true, "return":"T", "inputs": {
			"p2": {"n": "Find Word", "types": ["V", "T"], "m": true, "v": {"t":"T","v":"foo"}},
			"p3": {"n": "Replace With", "types": ["V", "T"], "m": true, "v": {"t":"T","v":"bar"}},
		}},
	},
	"N": {
		"get": {"self":false,"return":"self"},
		"set": {"self":true,"return":"N","inputs":{
			"p2": {"n": "Value", "m": true, "types": ["V", "N"], "v": {"t":"N","v":"0"}},
		}},
		"add": {
			"self": true, "replace": true, "return":"N",
			"inputs": {
				"p2": {"n": "Number", "m": true, "types": ["N","V"], "v": {"t":"N","v":"0"}},
			}
		},
		"subtract": {
			"self": true, "replace": true, "return":"N",
			"inputs": {
				"p2": {"n": "Number", "m": true, "types": ["N","V"], "v": {"t":"N","v":"0"}},
			}
		},
		"round": {
			"self": true, "replace": true, "return":"N",
			"inputs": {
				"p2": {"n": "Decimals", "m": true, "types": ["N"], "v": {"t":"N","v":"0"}  },
			}
		},
		"floor": {
			"self": true, "replace": true, "return":"N",
		},
		"ceil": {
			"self": true, "replace": true, "return":"N",
		},
		"textPadding": {
			"self": false,"return":"T",
			"inputs": {
				"p2": {"n": "Padding", "m": true, "types": ["N"], "v": {"t":"N","v":"10"}}, 
				"p3": {"n": "Symbol", "m": true, "types": ["T"], "v": {"t":"T","v":"X"}}, 
				"p4": {"n": "Mode", "m": true, "types": ["padding_modes"], "v":{"t": "padding_modes", "v": "Right"}}
			}
		},
		"convertToText": {"self":false,"return":"T"},
		"parseInt": {"self":false,"return":"N"},
	},
	"L": {
		"get": {"self":false,"return":"self"},
		"set": {"self":true,"return":"B","inputs":{
			"p2": {"n": "Value", "m": true, "types": ["V", "L"], "v":{"t": "L", "v": ""}},
		}},
		"setItem":{"self": true,"return":"B", "inputs":{
			"p2": {"n": "Index", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
			"p3": {"n": "Item", "types": ["V", "O", "T", "N"], "m": true, "v":{"t": "V", "v": {"v":"", "t":""} }},
		}},
		"setTemplate":{"self": true,"return":"B", "inputs":{
			"p2": {"n": "Template", "types": ["L"], "m": true, "v":{"t": "L", "v": [{"t":"O","v":{"key":{"k":"key","t":"T","v":""}}}] }},
		}},
		"length":{"self": false,"return":"N"},
		"getItem":{"self": false,"return":"O", "inputs":{
			"p2": {"n": "Item", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
		}},
		"append":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Item", "types": ["V", "T", "N", "L", "O", "B"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"prepend":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Item", "types": ["V", "T", "N", "L", "O", "B"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"insert":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Index", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
			"p3": {"n": "Item", "types": ["V", "T", "N", "L", "O", "B"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"remove":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Index", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
		}},
		"push":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Item", "types": ["V", "T", "N", "L", "O", "B"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"pop":{"self": true, "replace": false, "return":"O"},
	},
	"O": {
		"hasKey":{"self": false,"return":"O", "inputs":{
			"p2": {"n": "Key", "types": ["V", "T"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"getKey":{"self": false,"return":"O", "inputs":{
			"p2": {"n": "Key", "types": ["V", "T"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"getKeyList":{"self": false,"return":"L"},
		"setKey":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Key", "types": ["V", "T"], "m": true, "v":{"t": "T", "v": ""}},
			"p3": {"n": "Value", "types": ["V", "T", "N", "L", "O", "B"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"setTemplate":{"self": true,"return":"B", "inputs":{
			"p2": {"n": "Template", "types": ["L"], "m": true, "v":{"t":"O", "v":{"key":{"k":"key","t":"T","v":"sample"}} }},
		}},
		"setTemplate":{"self": true,"return":"B", "inputs":{
			"p2": {"n": "Template", "types": ["O"], "m": true, "v":{"t":"O", "v":{"key":{"k":"key","t":"T","v":""}} }},
		}},
		"removeKey":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Key", "types": ["V", "T"], "m": true, "v":{"t": "T", "v": ""}},
		}},
	},
	"B": {
		"setTrue":{"self": true, "replace": true, "return":"B"},
		"setFalse":{"self": true, "replace": true, "return":"B"},
		"set":{"self": true, "replace": true, "return":"B", "inputs":{
			"p3": {"n": "Value", "types": ["V", "B"], "m": true, "v": { "t": "B", "v":"true"}},
		}},
	},
	"D": {
		"get":{"self": false,"return":"D"},
		"set":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Date", "types": ["D","V"], "m": true, "v": { "t": "D", "v":"2023-03-03"}},
		}},
		"setValue":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Year", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"2023"}},
			"p3": {"n": "Month", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
			"p4": {"n": "Date", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
		}},
		"getDate":{"self": false, "replace": false, "return":"N"},
		"getMonth":{"self": false, "replace": false, "return":"N"},
		"getMonthFull":{"self": false, "replace": false, "return":"T"},
		"getMonthShort":{"self": false, "replace": false, "return":"T"},
		"getYear":{"self": false, "replace": false, "return":"N"},
		"getDayFull":{"self": false, "replace": false, "return":"T"},
		"getDayShort":{"self": false, "replace": false, "return":"T"},
		"getDaysTillToday":{"self": false, "replace": false, "return":"N"},
		"getDaysUntilToday":{"self": false, "replace": false, "return":"N"},
		"getDaysTill":{"self": false, "replace": false, "return":"N", "inputs":{
			"p2": {"n": "Date", "types": ["D","V"], "m": true, "v": { "t": "D", "v":"2023-01-01"}},
		}},
		"getDaysUntil":{"self": false, "replace": false, "return":"N", "inputs":{
			"p2": {"n": "Date", "types": ["D","V"], "m": true, "v": { "t": "D", "v":"2023-01-01"}},
		}},
		"getFormat":{"self": false, "replace": false, "return":"T", "h": "date.html", "inputs":{
			"p2": {"n": "DateFormat", "types": ["DFormat"], "m": true, "v": { "t": "DFormat", "v":"yyyy-mm-dd"}},
		}},
		"addDays":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractDays":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addMonths":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractMonths":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addYears":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractYears":{"self": true, "replace": true, "return":"D", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
	},
	"DT": {
		"set":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "DateTime", "types": ["T","V"], "m": true, "v": { "t": "T", "v":""}},
			"p3": {"n": "TimeZone", "types": ["TimeZone","V"], "m": true, "v": { "t": "TimeZone", "v":""}},
		}},
		"setValue":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Year", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"2023"}},
			"p3": {"n": "Month", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
			"p4": {"n": "Date", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
			"p5": {"n": "Hours", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p6": {"n": "Minutes", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p7": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p8": {"n": "TimeZone", "types": ["TimeZone"], "m": true, "v": { "t": "TimeZone", "v":"UTC+00:00"}},
		}},
		"setTimeStamp":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Unix Timestamp", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1697741273"}},
			"p3": {"n": "TimeZone", "types": ["TimeZone"], "m": true, "v": { "t": "TimeZone", "v":"UTC+00:00"}},
		}},
		"getDate":{"self": false, "replace": false, "return":"N"},
		"getMonth":{"self": false, "replace": false, "return":"N"},
		"getMonthFull":{"self": false, "replace": false, "return":"T"},
		"getMonthHalf":{"self": false, "replace": false, "return":"T"},
		"getYear":{"self": false, "replace": false, "return":"N"},
		"getDayIndex":{"self": false, "replace": false, "return":"N"},
		"getDayFull":{"self": false, "replace": false, "return":"T"},
		"getDayShort":{"self": false, "replace": false, "return":"T"},
		"getHours":{"self": false, "replace": false, "return":"N"},
		"getMinutes":{"self": false, "replace": false, "return":"N"},
		"getSeconds":{"self": false, "replace": false, "return":"N"},
		"getTimeZone":{"self": false, "replace": false, "return":"T"},
		"getTimeStamp":{"self": false, "replace": false, "return":"N"},
		"setTimeZone":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "TimeZone", "types": ["TimeZone","V"], "m": true, "v": { "t": "TimeZone", "v":"10"}},
		}},
		"getDaysTillToday":{"self": false, "replace": false, "return":"N"},
		"getDaysUntilToday":{"self": false, "replace": false, "return":"N"},
		"getDaysTill":{"self": false, "replace": false, "return":"N", "inputs":{
			"p2": {"n": "Date", "types": ["D","V"], "m": true, "v": { "t": "D", "v":"2023-01-01"}},
		}},
		"getDaysUntil":{"self": false, "replace": false, "return":"N", "inputs":{
			"p2": {"n": "Date", "types": ["D","V"], "m": true, "v": { "t": "D", "v":"2023-01-01"}},
		}},
		"addDays":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractDays":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addMonths":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractMonths":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addYears":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractYears":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addSeconds":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"86400"}},
		}},
		"subtractSeconds":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"86400"}},
		}},
	},
	"TS": {
		"get":{"self": false, "replace": false, "return":"N"},
		"set":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Unix Timestamp", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1697741273"}},
			"p3": {"n": "TimeZone", "types": ["TimeZone"], "m": true, "v": { "t": "TimeZone", "v":"UTC+00:00"}},
		}},
		"setValue":{"self": true, "replace": true, "return":"DT", "inputs":{
			"p2": {"n": "Year", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"2023"}},
			"p3": {"n": "Month", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
			"p4": {"n": "Date", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"01"}},
			"p5": {"n": "Hours", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p6": {"n": "Minutes", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p7": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"10"}},
			"p8": {"n": "TimeZone", "types": ["TimeZone"], "m": true, "v": { "t": "TimeZone", "v":"UTC+00:00"}},
		}},
		"getDate":{"self": false, "replace": false, "return":"N"},
		"getMonth":{"self": false, "replace": false, "return":"N"},
		"getMonthFull":{"self": false, "replace": false, "return":"T"},
		"getMonthHalf":{"self": false, "replace": false, "return":"T"},
		"getYear":{"self": false, "replace": false, "return":"N"},
		"getDayIndex":{"self": false, "replace": false, "return":"N"},
		"getDayFull":{"self": false, "replace": false, "return":"T"},
		"getDayShort":{"self": false, "replace": false, "return":"T"},
		"getHours":{"self": false, "replace": false, "return":"N"},
		"getMinutes":{"self": false, "replace": false, "return":"N"},
		"getSeconds":{"self": false, "replace": false, "return":"N"},
		"getTimeZone":{"self": false, "replace": false, "return":"T"},
		"addDays":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractDays":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Days", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addMonths":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractMonths":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Months", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addYears":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"subtractYears":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Years", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"1"}},
		}},
		"addSeconds":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"86400"}},
		}},
		"subtractSeconds":{"self": true, "replace": true, "return":"TS", "inputs":{
			"p2": {"n": "Seconds", "types": ["N","V"], "m": true, "v": { "t": "N", "v":"86400"}},
		}},
	},
	"TI": {
		"set":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Id", "types": ["T","V"], "m": true, "v": { "t": "T", "v":""}},
			"p3": {"n": "Label", "types": ["T","V"], "m": true, "v": { "t": "T", "v":""}},
		}},
		"get":{"self": false,"return":"TI"},
		"setId":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "ID", "types": ["T","V"], "m": true, "v": { "t": "T", "v":""}},
		}},
		"setLabel":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Label", "types": ["T","V"], "m": true, "v": { "t": "T", "v":""}},
		}},
		"getId":{"self": false, "return":"T"},
		"getLabel":{"self": false, "return":"T"},
	},
	"THL": {
		"getById": {"self":false,"return":"TI","inputs":{
			"p2": {"n": "Id", "m": true, "types": ["V", "T"], "v":{"t": "T", "v": ""}},
		}},
		"getByLabel": {"self":false,"return":"TI","inputs":{
			"p2": {"n": "Label", "m": true, "types": ["V", "T"], "v":{"t": "T", "v": ""}},
		}},
		"set": {"self":true,"return":"","inputs":{
			"p2": {"n": "Value", "m": true, "types": ["V"], "v":{"t": "V", "v": ""}},
		}},
		"length":{"self": false,"return":"N"},
		"getByIndex":{"self": false,"return":"TI","inputs":{
			"p2": {"n": "Index", "m": true, "types": ["N", "V"], "v":{"t": "N", "v": "1"}},
		}},
		"append":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Item", "types": ["TI", "V"], "m": true, "v":{"t": "TI", "v": {"i":"","l":""} }},
		}},
		"prepend":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Item", "types": ["TI", "V"], "m": true, "v":{"t": "TI", "v": {"i":"","l":""} }},
		}},
		"pop":{"self": true, "replace": false, "return":"TI"},
		"insert":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Index", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
			"p3": {"n": "Item", "types": ["TI", "V"], "m": true, "v":{"t": "TI", "v": {"i":"","l":""} }},
		}},
		"removeByIndex":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Index", "types": ["V", "N"], "m": true, "v":{"t": "N", "v": "0"}},
		}},
		"removeById":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Id", "types": ["T", "V"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"removeByLabel":{"self": true, "replace": false, "return":"B", "inputs":{
			"p2": {"n": "Label", "types": ["T", "V"], "m": true, "v":{"t": "T", "v": ""}},
		}},
		"sortById":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Order", "types": ["SortOrder"], "m": true, "v":{"t": "SortOrder", "v": "a-z"}},
		}},
		"sortByLabel":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Order", "types": ["SortOrder"], "m": true, "v":{"t": "SortOrder", "v": "a-z"}},
		}},
	},
	"MongoQ": {
		"addCondition":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Field", "types": ["T"], "m": true, "v": {"t": "T", "v":"field"}},
			"p3": {"n": "Operator", "types": ["MongoOp"], "m": true, "v": {"t": "MongoOp", "v":'$eq'}},
			"p4": {"n": "Value", "types": ["V","T","N","B","NL"], "m": true, "v": {"t": "T", "v":"value"}},
		}},
	},
	"MysqlQ": {
		"addCondition":{"self": true, "replace": true, "return":"B", "inputs":{
			"p2": {"n": "Field", "types": ["T"], "m": true, "v": {"t": "T", "v":"field"}},
			"p3": {"n": "Operator", "types": ["MysqlOp"], "m": true, "v": {"t": "MysqlOp", "v":'='}},
			"p4": {"n": "Value", "types": ["V","T","N", "B", "NL"], "m": true, "v": {"t": "T", "v":"value"}},
		}},
	},
};
