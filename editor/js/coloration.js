
function is_char_of_variable(character)
{
    return character>='0' && character<='9' || character>='A' && character<='Z' || character>='a' && character<='z' || character=='_';
}

function is_uppercase(string)
{
    for(let i=0; i<string.length; i++)
    {
        if((string[i] < 'A' || string[i] > 'Z') && (string[i] < '0' || string[i] > '9') && string[i] != "_")
        {
            return false;
        }
    }
    return true;
}

function color_line(string, i, line_number)
{
    if(line_number > 0)
        var flags = global_lines_to_display[line_number-1][0];
    else
        var flags = START_FLAGS;

    let result = "";
    
    // opening of the tags (for dom)
    if((flags&(BLOCK_CSS|VALUE_CSS)) == (BLOCK_CSS|VALUE_CSS))
    {
        result += "<cssvalue>";
    }
    if((flags&(BLOCK_CSS|VALUE_CSS)) == BLOCK_CSS)
    {
        result += "<cssproperty>";
    }
    if((flags&(BALISE_HTML|ARGS_HTML)) == BALISE_HTML)
    {
        result += "<balise>";
    }
    if((flags&ARGS_HTML) != 0)
    {
        result += "<argument>";
    }
    if((flags&(LONG_COMMENT|SHORT_COMMENT)) != 0)
    {
        result += "<comment>";
    }
    if((flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES)) != 0)
    {
        result += "<string>";
    }
    
    while(i < string.length && string[i] != "\n")
    {
        if((string[i-1] == "<" || string[i-1] == " ") && string.substring(i, i+6) == "script" && (string[i+6] == ">" || string[i+6] == " ") && (flags&(HTML|BALISE_HTML|ARGS_HTML|SCRIPT_BALISE)) == (HTML|BALISE_HTML))
        {
            flags = flags|SCRIPT_BALISE;
        }
        if((string[i-1] == "<" || string[i-1] == " ") && string.substring(i, i+5) == "style" && (string[i+5] == ">" || string[i+5] == " ") && (flags&(HTML|BALISE_HTML|ARGS_HTML|STYLE_BALISE)) == (HTML|BALISE_HTML))
        {
            flags = flags|STYLE_BALISE;
        }
        
        if(string.substring(i, i+5) == "<"+"?"+"php" && !is_char_of_variable(string[i+5]) && (flags&PHP) == 0)
        {
            if((flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES)) != 0)
            {
                result += "</string>";
            }
            if((flags&(LONG_COMMENT|SHORT_COMMENT)) != 0)
            {
                result += "</comment>";
            }
            if((flags&ARGS_HTML) != 0)
            {
                result += "</argument>";
            }
            if((flags&(BALISE_HTML|ARGS_HTML)) == BALISE_HTML)
            {
                result += "</balise>";
            }
            if((flags&(BLOCK_CSS|VALUE_CSS)) == BLOCK_CSS)
            {
                result += "</cssproperty>";
            }
            if((flags&(BLOCK_CSS|VALUE_CSS)) == (BLOCK_CSS|VALUE_CSS))
            {
                result += "</cssvalue>";
            }
            global_flags_before_php = flags;
            flags = PHP;
            result += "&lt;<balise>?php</balise>";
            i += 5;
        }
        else if(string[i] == "?" && string[i+1] == ">" && (flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT|PHP)) == PHP)
        {
            result += "<balise>?</balise>&gt;";
            i += 2;
            flags = global_flags_before_php;
            if((flags&(BLOCK_CSS|VALUE_CSS)) == (BLOCK_CSS|VALUE_CSS))
            {
                result += "<cssvalue>";
            }
            if((flags&(BLOCK_CSS|VALUE_CSS)) == BLOCK_CSS)
            {
                result += "<cssproperty>";
            }
            if((flags&(BALISE_HTML|ARGS_HTML)) == BALISE_HTML)
            {
                result += "<balise>";
            }
            if((flags&ARGS_HTML) != 0)
            {
                result += "<argument>";
            }
            if((flags&(LONG_COMMENT|SHORT_COMMENT)) != 0)
            {
                result += "<comment>";
            }
            if((flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES)) != 0)
            {
                result += "<string>";
            }
        }
        
        
        else if(string[i] == "&" && (flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT|HTML)) == 0)
        {
            result += "<operator>&amp;</operator>";
            i++;
        }
        else if(string[i] == "<" && (flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT|HTML)) == 0)
        {
            if(string.substring(i, i+8) == "</script")
            {
                result += "&lt;/<balise>script</balise>";
                i += 8;
                flags = flags|HTML|BALISE_HTML;
            }
            else if(string.substring(i, i+7) == "</style")
            {
                result += "&lt;/<balise>style</balise>";
                i += 7;
                if((flags&CSS) == CSS)
                {
                    flags = flags - CSS;
                }
                flags = flags|HTML|BALISE_HTML;
            }
            else
            {
                result += "<operator>&lt;</operator>";
                i++;
            }
        }
        else if(string[i] == ">" && (flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT|HTML)) == 0)
        {
            result += "<operator>&gt;</operator>";
            i++;
        }
        
        
        else if(string.substring(i, i+4) == "<!--" && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|HTML)) == HTML)
        {
            result += "<comment>&lt;!--";
            i += 4;
            flags = flags|LONG_COMMENT
        }
        else if(string.substring(i, i+3) == "-->" && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|HTML)) == (LONG_COMMENT|HTML))
        {
            result += "--&gt;</comment>";
            i += 3;
            flags = flags - LONG_COMMENT;
        }
        
        
        else if(string[i] == "<")
        {
            result += "&lt;";
            i++;
            if((flags&(HTML|BALISE_HTML|ARGS_HTML|STYLE_BALISE|LONG_COMMENT)) == HTML)
            {
                if(string[i] == "/")
                {
                    result += "/";
                    i++;
                }
                flags = flags|BALISE_HTML;
                result += "<balise>";
            }
        }
        else if(string[i] == ">")
        {
            if((flags&(HTML|BALISE_HTML|LONG_COMMENT)) == (HTML|BALISE_HTML))
            {
                if((flags&ARGS_HTML) == ARGS_HTML)
                {
                    flags = flags - ARGS_HTML;
                    result += "</argument>";
                }
                else
                {
                    result += "</balise>";
                }
                flags = flags - BALISE_HTML;
                
                if((flags&SCRIPT_BALISE) == SCRIPT_BALISE)
                {
                    flags = flags - SCRIPT_BALISE;
                    flags = flags - HTML;
                }
                else if((flags&STYLE_BALISE) == STYLE_BALISE)
                {
                    flags = flags - STYLE_BALISE;
                    flags = flags - HTML;
                    flags = flags|CSS;
                }
            }
            result += "&gt;"
            i++;
        }
        else if(string[i] == "&")
        {
            result += "&amp;"
            i++;
        }
        
        
        else if((flags&(HTML|BALISE_HTML)) == (HTML|BALISE_HTML))
        {
            if(string[i] == " " && (flags&ARGS_HTML) == 0)
            {
                flags = flags|ARGS_HTML;
                result += "</balise><argument>";
            }
            else if(string[i] == '"' && (string[i-1]!='\\' || string[i-2]=='\\') && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES)) == 0)
            {
                i++;
                result += '<string>"';
                flags = flags|DOUBLE_QUOTES;
            }
            else if(string[i]=='"' && (flags&SIMPLE_QUOTES) == 0 && (string[i-1]!='\\' || string[i-2]=='\\'))
            {
                i++;
                result += '"</string>';
                flags = flags - DOUBLE_QUOTES;
            }
            else if(string[i] == "=" && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES)) == 0)
            {
                result += "<operator>=</operator>";
                i++;
            }
            else
            {
                result += string[i];
                i++;
            }
        }
        
        else if((flags&HTML) == HTML)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string[i] == "/" && string[i+1] == "*" && (flags&(CSS|LONG_COMMENT)) == CSS)
        {
            i += 2;
            result += "<comment>/*";
            flags = flags|LONG_COMMENT;
        }
        else if(string[i] == "*" && string[i+1] == "/" && (flags&(CSS|LONG_COMMENT)) == (CSS|LONG_COMMENT))
        {
            i += 2;
            result += "*/</comment>";
            flags = flags - LONG_COMMENT;
        }
        else if((flags&(CSS|LONG_COMMENT)) == (CSS|LONG_COMMENT))
        {
            result += string[i];
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS)) == CSS && string[i] == "#")
        {
            result += "<cssid>#";
            i++;
            while(is_char_of_variable(string[i]) || string[i] == "-")
            {
                result += string[i];
                i++;
            }
            result += "</cssid>";
        }
        else if((flags&(CSS|BLOCK_CSS)) == CSS && string[i] == ".")
        {
            result += "<cssclass>.";
            i++;
            while(is_char_of_variable(string[i]) || string[i] == "-")
            {
                result += string[i];
                i++;
            }
            result += "</cssclass>";
        }
        else if((flags&(CSS|BLOCK_CSS)) == CSS && is_char_of_variable(string[i]))
        {
            result += "<balise>";
            while(is_char_of_variable(string[i]))
            {
                result += string[i];
                i++;
            }
            result += "</balise>";
        }
        else if((flags&(CSS|BLOCK_CSS)) == CSS && string[i] == "{")
        {
            flags = flags|BLOCK_CSS;
            result += "{<cssproperty>";
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS)) == (CSS|BLOCK_CSS) && string[i] == "}")
        {
            flags = flags - BLOCK_CSS;
            if((flags&VALUE_CSS) == VALUE_CSS)
            {
                result += "</cssvalue>}";
                flags = flags - VALUE_CSS;
            }
            else
            {
                result += "</cssproperty>}";
            }
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS) && string[i] == ":")
        {
            flags = flags|VALUE_CSS;
            result += "</cssproperty>:<cssvalue>";
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && string[i] == ";")
        {
            flags = flags - VALUE_CSS;
            result += "</cssvalue>;<cssproperty>";
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && string[i] >= '0' && string[i] <= '9')
        {
            result += '<number>';
            while(string[i] >= '0' && string[i] <= '9')
            {
                result += string[i];
                i++;
            }
            result += "</number>";
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && string[i] == "#")
        {
            result += '<numberhexa>#';
            i++;
            while(string[i] >= '0' && string[i] <= '9' || string[i] >= 'A' && string[i] <= 'F' || string[i] >= 'a' && string[i] <= 'f')
            {
                result += string[i];
                i++;
            }
            result += "</numberhexa>";
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && (string[i] == "%" || string[i] == "s") && !is_char_of_variable(string[i+1]) && (!is_char_of_variable(string[i-1]) || string[i-1] >= '0' && string[i-1] <= '9'))
        {
            result += '<cssunit>' + string[i] + '</cssunit>';
            i++;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && ["px", "pt", "pc", "in", "cm", "mm", "em", "ch", "ex", "lh", "vw", "vh", "fr", "ms", "hz"].includes(string.substring(i, i+2)) && !is_char_of_variable(string[i+2]) && (!is_char_of_variable(string[i-1]) || string[i-1] >= '0' && string[i-1] <= '9'))
        {
            result += '<cssunit>' + string[i] + string[i+1] + '</cssunit>';
            i += 2;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && ["rem", "deg", "rad", "dpi", "khz"].includes(string.substring(i, i+3)) && !is_char_of_variable(string[i+3]) && (!is_char_of_variable(string[i-1]) || string[i-1] >= '0' && string[i-1] <= '9'))
        {
            result += '<cssunit>' + string.substring(i, i+3) + '</cssunit>';
            i += 3;
        }
        else if((flags&(CSS|BLOCK_CSS|VALUE_CSS)) == (CSS|BLOCK_CSS|VALUE_CSS) && ["vmin", "vmax", "grad", "turn", "dpcm", "dppx"].includes(string.substring(i, i+4)) && !is_char_of_variable(string[i+4]) && (!is_char_of_variable(string[i-1]) || string[i-1] >= '0' && string[i-1] <= '9'))
        {
            result += '<cssunit>' + string.substring(i, i+4) + '</cssunit>';
            i += 4;
        }
        else if((flags&CSS) == CSS)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string.substring(i, i+3)=='"""' && (string[i-1]!='\\' || string[i-2]=='\\') && (flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT)) == 0)
        {
            i += 3;
            result += '<string>"""';
            flags = flags|TRIPLE_QUOTES;
        }
        else if(string.substring(i, i+3)=='"""' && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT)) == 0 && (string[i-1]!='\\' || string[i-2]=='\\'))
        {
            i += 3;
            result += '"""</string>';
            flags = flags - TRIPLE_QUOTES;
        }
        else if((flags&TRIPLE_QUOTES) == TRIPLE_QUOTES)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string[i]=='"' && (string[i-1]!='\\' || string[i-2]=='\\') && (flags&(DOUBLE_QUOTES|SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT)) == 0)
        {
            i++;
            result += '<string>"';
            flags = flags|DOUBLE_QUOTES;
        }
        else if(string[i]=='"' && (flags&(SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT)) == 0 && (string[i-1]!='\\' || string[i-2]=='\\'))
        {
            i++;
            result += '"</string>';
            flags = flags - DOUBLE_QUOTES;
        }
        else if((flags&DOUBLE_QUOTES) == DOUBLE_QUOTES)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string[i]=="'" && (string[i-1]!='\\' || string[i-2]=='\\') && (flags&(SIMPLE_QUOTES|LONG_COMMENT|SHORT_COMMENT)) == 0)
        {
            i++;
            result += "<string>'";
            flags = flags|SIMPLE_QUOTES;
        }
        else if(string[i]=="'" && (flags&(LONG_COMMENT|SHORT_COMMENT)) == 0 && (string[i-1]!='\\' || string[i-2]=='\\'))
        {
            i++;
            result += "'</string>";
            flags = flags - SIMPLE_QUOTES;
        }
        else if((flags&SIMPLE_QUOTES) == SIMPLE_QUOTES)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string[i] == "/" && string[i+1] == "*" && (flags&(LONG_COMMENT|SHORT_COMMENT)) == 0)
        {
            i += 2;
            result += "<comment>/*";
            flags = flags|LONG_COMMENT;
        }
        else if(string[i] == "*" && string[i+1] == "/" && (flags&(LONG_COMMENT|SHORT_COMMENT)) == LONG_COMMENT)
        {
            i += 2;
            result += "*/</comment>";
            flags = flags - LONG_COMMENT;
        }
        else if((flags&LONG_COMMENT) == LONG_COMMENT)
        {
            result += string[i];
            i++;
        }
        
        
        else if(string[i] == "/" && string[i+1] == "/" && (flags&SHORT_COMMENT) == 0)
        {
            i += 2;
            result += "<comment>//";
            flags = flags|SHORT_COMMENT;
        }
        else if(string[i]=="#" && (flags&SHORT_COMMENT) == 0)
        {
            i++;
            let a = i;
            while(string[a] == " ")
            {
                a++;
            }
            if(string.substring(a, a+7) == "include" || string.substring(a, a+6) == "define")
            {
                result += "<operator>#</operator>";
            }
            else
            {
                result += "<comment>#";
                flags = flags|SHORT_COMMENT;
            }
        }
        else if((flags&SHORT_COMMENT) == SHORT_COMMENT)
        {
            result += string[i];
            i++;
        }
        
        
        else if(is_char_of_variable(string[i]) && (string[i]<'0' || string[i]>'9') || string[i] == "$")
        {
            let mot = string[i];
            i++;
            while(is_char_of_variable(string[i]))
            {
                mot += string[i];
                i++;
            }
            let a = i;
            while(string[a] == " ")
            {
                a++;
            }
            const keywords=["elif", "if", "else", "switch", "case", "default", "from", "import", "include", "define", "foreach", "del", "async", "await", "def", "function", "class", "while", "for", "in", "or", "and", "not", "is", "as", "with", "undefined", "break", "pass", "continue", "return", "try", "except", "raise", "catch", "global", "extern", "assert", "finally", "lambda", "yield", "do", "goto"];
            
            const non_case_sensitive_keywords = ["none", "false", "true", "null"];
            
            const types=["int", "var", "let", "const", "bool", "char", "float", "double", "str", "string", "list", "array", "long", "short", "unsigned", "signed", "void", "struct"];
            
            if(keywords.includes(mot))
            {
                result += "<keyword>"+mot+"</keyword>";
            }
            else if(non_case_sensitive_keywords.includes(mot.toLowerCase()))
            {
                result += "<keyword>"+mot+"</keyword>";
            }
            else if(types.includes(mot))
            {
                result += "<type>"+mot+"</type>";
            }
            else if(string[a] == "(")
            {
                result += "<function>"+mot+"</function>";
            }
            else if(is_uppercase(mot))
            {
                result += "<constant>"+mot+"</constant>";
            }
            else if(mot == "this" || mot == "self")
            {
                result += "<argument>"+mot+"</argument>";
            }
            else
            {
                result += "<variable>"+mot+"</variable>";
            }
        }
        
        
        else if(string[i] == "0" && (string[i+1] == "x" || string[i+1] == "X" || string[i+1] == "o" || string[i+1] == "O"))
        {
            result += '<number>0' + string[i+1];
            i += 2
            while(string[i] >= '0' && string[i] <= '9' || string[i] >= 'A' && string[i] <= 'F' || string[i] >= 'a' && string[i] <= 'f')
            {
                result += string[i];
                i++;
            }
            result += "</number>";
        }
        else if(string[i] >= '0' && string[i] <= '9')
        {
            result += '<number>'
            while(string[i] >= '0' && string[i] <= '9')
            {
                result += string[i];
                i++;
            }
            result += "</number>";
        }
        
        
        else if(string[i] == '+' || string[i] == '-' || string[i] == '*' || string[i] == '/' || string[i] == '=' || string[i] == '!' || string[i] == '|' || string[i] == '^' || string[i] == '~' || string[i] == '%')
        {
            result += '<operator>'
            while(string[i] == '+' || string[i] == '-' || string[i] == '*' || string[i] == '/' || string[i] == '=' || string[i] == '!' || string[i] == '|' || string[i] == '^' || string[i] == '~' || string[i] == '%')
            {
                result += string[i];
                i++;
            }
            result += "</operator>";
        }
        
        
        else
        {
            result += string[i];
            i++;
        }
    }
    
    if((flags&SHORT_COMMENT) == SHORT_COMMENT)
    {
        result += "</comment>";
        flags -= SHORT_COMMENT;
    }
    
    // closing the tags (for dom)
    
    if((flags&(TRIPLE_QUOTES|DOUBLE_QUOTES|SIMPLE_QUOTES)) != 0)
    {
        result += "</string>";
    }
    if((flags&(LONG_COMMENT|SHORT_COMMENT)) != 0)
    {
        result += "</comment>";
    }
    if((flags&ARGS_HTML) != 0)
    {
        result += "</argument>";
    }
    if((flags&(BALISE_HTML|ARGS_HTML)) == BALISE_HTML)
    {
        result += "</balise>";
    }
    if((flags&(BLOCK_CSS|VALUE_CSS)) == BLOCK_CSS)
    {
        result += "</cssproperty>";
    }
    if((flags&(BLOCK_CSS|VALUE_CSS)) == (BLOCK_CSS|VALUE_CSS))
    {
        result += "</cssvalue>";
    }
    
    global_lines_to_display[line_number][0] = flags;
    global_lines_to_display[line_number][1] = result;

    return [flags, i+1];
}