
global_lines_to_display = [];
global_autocompletion_running = false;

lines_init(START_FLAGS);

function lines_init(flags)
{
    global_lines_to_display = [];
    global_flags_before_php = START_FLAGS;
    let area = document.getElementById("code_area");
    let display_screen = document.getElementById("display_screen");
    let count = 0;
    for(let i = 0; i<area.value.length; i++)
    {
        let temp_string = "";
        while(i<area.value.length && area.value[i] != "\n")
        {
            temp_string += area.value[i];
            i++;
        }
        global_lines_to_display = global_lines_to_display.concat([[flags, temp_string]]);
        flags = color_line(temp_string, 0, count)[0];
        count++;
    }
    global_lines_to_display = global_lines_to_display.concat([[flags, '']]);
    
    edit_words(area.value);
    
    let temp_string = "";
    for(let i = 0; i < global_lines_to_display.length; i++)
    {
        temp_string += global_lines_to_display[i][1] + '<br>';
        
    }
    
    display_screen.innerHTML = temp_string;
    
    area.style.height = '500px';
    let height = area.scrollHeight + 'px';
    area.style.height = height;
    document.getElementById("lines").style.height = height;
    document.getElementById("save_button").style.top = height;
    document.getElementById("save_close_button").style.top = height;
    
    global_flags_before_php = START_FLAGS;
}
setInterval(lines_init, 3000);

function get_line_start(area)
{
    let cursor = area.selectionStart - 1;
    while(cursor >= 0 && area.value[cursor] != "\n")
    {
        cursor--;
    }
    return cursor + 1;
}

function get_line_number(area)
{
    let n = 0;
    let cursor = area.selectionStart - 1;
    while(cursor >= 0)
    {
        if(area.value[cursor] == "\n")
        {
            n++;
        }
        cursor--;
    }
    return n;
}

function display_line(display_screen, line_number)
{
    let n = 1;
    let i = 0;
    while(i < display_screen.childNodes.length && n < line_number)
    {
        if(display_screen.childNodes[i].nodeName == "BR")
        {
            n++;
        }
        i++
    }
    
    while(i < display_screen.childNodes.length && display_screen.childNodes[i].nodeName != "BR")
    {
        display_screen.childNodes[i].remove();
    }
    let el = new DOMParser().parseFromString(global_lines_to_display[line_number-1][1], "text/html")
    for(let a = el.body.childNodes.length - 1; a >= 0; a--)
    {
        display_screen.insertBefore(el.body.childNodes[a], display_screen.childNodes[i]);
    }
    if(global_lines_to_display[line_number-1][1][0] == " ")
    {
        let a = 0;
        spaces = "";
        while(global_lines_to_display[line_number-1][1][a] == " ")
        {
            spaces += " ";
            a++;
        }
        display_screen.insertBefore(document.createTextNode(spaces), display_screen.childNodes[i]);
    }
}


function show()
{
    // var stime = Date.now();
    
    let code_area = document.getElementById("code_area");
    let display_screen = document.getElementById("display_screen");
    
    let start = get_line_start(code_area);
    let line_number = get_line_number(code_area);
    let last_flags = global_lines_to_display[line_number][0];
    
    let out = color_line(code_area.value, start, line_number);
    
    display_line(display_screen, line_number+1);
    
    let propagation = false;
    while((line_number++) < global_lines_to_display.length - 1 && out[0] != last_flags)
    {
        // the flags are different... it is necessary to propagate
        last_flags = global_lines_to_display[line_number][0];
        out = color_line(code_area.value, out[1], line_number);
        propagation = true;
    }
    if(propagation)
    {
        let temp_string = "";
        for(let i = 0; i < global_lines_to_display.length; i++)
        {
            temp_string += global_lines_to_display[i][1] + '<br>';
            
        }
        
        display_screen.innerHTML = temp_string;
    }
    
    code_area.style.height = '500px';
    let height = code_area.scrollHeight + 'px';
    code_area.style.height = height;
    document.getElementById("lines").style.height = height;
    document.getElementById("save_button").style.top = height;
    document.getElementById("save_close_button").style.top = height;
    
    // console.log(Date.now() - stime);
}
setInterval(show, 100);



let textareas = document.getElementsByTagName('textarea');
let count = textareas.length;

for(let i=0;i<count;i++)
{
    textareas[i].ondragend = function(e)
    {
        lines_init(START_FLAGS);
    }
    textareas[i].onpaste = function(e)
    {
        let el = this;
        setTimeout(function() {
            let s = el.selectionStart;
            el.value = el.value.replace(/\t/g, "    ");
            el.selectionStart = el.selectionEnd = s;
            lines_init(START_FLAGS);
        }, 100);
    }
    textareas[i].oncut = function(e)
    {
        setTimeout(function() {
            lines_init(START_FLAGS);
        }, 100);
    }
    textareas[i].onkeydown = function(e)
    {
        if(e.keyCode==8 || e.which==8)
        {
            let c = this.value[this.selectionStart - 1];
            let c_after = this.value[this.selectionStart];
            if(c == "\n")
            {
                setTimeout(function() {
                    lines_init(START_FLAGS);
                }, 100);
            }
            else if(this.selectionStart == this.selectionEnd && (c == "(" && c_after == ")" || c == "[" && c_after == "]" || c == "{" && c_after == "}" || c == "'" && c_after == "'" || c == '"' && c_after == '"'))
            {
                let s = this.selectionStart;
                this.value = this.value.substring(0, this.selectionStart) + this.value.substring(this.selectionEnd+1);
                this.selectionStart = this.selectionEnd = s;
            }
        }
        else if(e.keyCode==13 || e.which==13)
        {
            CancelEvent();
            if(global_active_suggestion !== false && !e.shiftKey)
            {
                let a = this.selectionStart;
                let i = a;
                while(is_char_of_variable(this.value[i-1]))
                {
                    i--;
                }
                while(is_char_of_variable(this.value[a]))
                {
                    a++;
                }
                insert_suggestion(global_active_suggestion, i, a, global_active_suggestion.length + i);
            }
            else
            {
                let content=this.value;
                let a=this.selectionStart;
                let indentations='';
                let indentations2='';
                let auto_indent = false;
                if(content[this.selectionStart-1]=='{' || content[this.selectionStart-1]=='(' || content[this.selectionStart-1]=='[' || content[this.selectionStart-1]==':')
                {
                    auto_indent = true;
                }
                while(a>0 && content[a-1]!='\n')
                {
                    a--;
                }
                while(content[a]==" " || content[a]=='\t')
                {
                    indentations+=" ";
                    if(content[a]=="\t")
                    {
                        indentations+="    "
                    }
                    a++;
                }
                if(auto_indent)
                {
                    if(content[this.selectionStart-1]=='{' && content[this.selectionStart]=='}' || content[this.selectionStart-1]=='(' && content[this.selectionStart]==')' || content[this.selectionStart-1]=='[' && content[this.selectionStart]==']')
                    {
                        indentations2 = "\n" + indentations;
                    }
                    indentations += "    ";
                }
                
                let s = this.selectionStart;
                this.value = this.value.substring(0,this.selectionStart) + "\n" + indentations + indentations2 + this.value.substring(this.selectionEnd);
                this.selectionEnd = s+indentations.length+1;
                
                
                lines_init(START_FLAGS);
            }
        }


        
        else if((e.keyCode==9 || e.which==9) && e.shiftKey)
        {
            
            CancelEvent(e);
            if(this.selectionStart == this.selectionEnd)
            {
                let i = this.selectionStart-1;
                while(this.selectionStart - i < 4 && this.value[i] == ' ')
                {
                    i--;
                }
                this.value = this.value.substring(0,i) + this.value.substring(this.selectionEnd);
                this.selectionEnd -= 4;
                
            }
            else
            {
                let i = this.selectionStart;
                while(i > 0 && this.value[i] != '\n')
                {
                    i--;
                }
                let start = i;
                let end = i;
                let out = this.value.substring(0, i);
                while(i < this.selectionEnd)
                {
                    if(i == 0 || this.value[i-1] == '\n')
                    {
                        let a = 0;
                        while(a < 4 && this.value[i] == ' ')
                        {
                            i++;
                            a++;
                        }
                    }
                    out += this.value[i];
                    i++;
                    end++;
                }
                out += this.value.substring(this.selectionEnd);
                this.value = out;
                if(start == 0)
                {
                    start--;
                }
                if(end == this.value.length)
                {
                    end++;
                }
                this.selectionStart = start+1;
                this.selectionEnd = end;
            }
        }
        
        else if(e.keyCode==9 || e.which==9)
        {
            CancelEvent(e);
            if(this.selectionStart == this.selectionEnd)
            {
                let s = this.selectionStart;
                this.value = this.value.substring(0,this.selectionStart) + "    " + this.value.substring(this.selectionEnd);
                this.selectionEnd = s+4;
            }
            else
            {
                this.selectionEnd++;
                let i = this.selectionStart;
                let start = i;
                let end = i;
                let out = this.value.substring(0,this.selectionStart);
                while(i < this.selectionEnd)
                {
                    out += "    ";
                    end += 4;
                    while(i < this.selectionEnd && this.value[i]!='\n')
                    {
                        out += this.value[i];
                        i++;
                        end++;
                    }
                    if(i < this.value.length)
                    {
                        out += this.value[i];
                        i++;
                    }
                    end++;
                }
                out += this.value.substring(this.selectionEnd);
                this.value = out;
                this.selectionStart = start;
                this.selectionEnd = end-1;
                
            }

        }
        
        else if(e.keyCode==40 || e.which==40)
        {
            if(global_active_suggestion !== false)
            {
                CancelEvent(e);
                global_active_suggestion = global_suggestions[global_suggestions.indexOf(global_active_suggestion)+1];
                suggest();
            }
        }
        
        else if(e.keyCode==39 || e.which==39)
        {
            if(global_active_suggestion !== false)
            {
                CancelEvent(e);
                document.getElementById("suggestions").style.display = "none";
                global_active_suggestion = false;
                global_suggestions = [];
                global_disabled_suggestions = true;
            }
        }
        
        else if(e.keyCode==32 || e.which==32)
        {
            global_disabled_suggestions = false;
        }
        
        
        if(this.selectionStart != this.selectionEnd && (e.keyCode < 16 || e.keyCode > 18) && (e.which < 16 || e.which > 18))
        {
            let selection_before = this.value.substring(this.selectionStart, this.selectionEnd);
            let el = this;
            global_autocompletion_running = true;
            setTimeout(function() {
                if(e.keyCode!=8 && e.which!=8 && (!e.ctrlKey || e.altKey))
                {
                    let c = el.value[el.selectionStart-1];
                    if(c == '"' || c == "'" || c == '(' || c == '[' || c == '{')
                    {
                        if(c == '(')
                            c = ')';
                        else if(c == '[')
                            c = ']';
                        else if(c == '{')
                            c = '}';
                        
                        let s = el.selectionStart;
                        el.value = el.value.substring(0, el.selectionStart) + selection_before + c + el.value.substring(el.selectionEnd);
                        el.selectionStart = s;
                        el.selectionEnd = s + selection_before.length;
                    }
                }
                global_autocompletion_running = false;
                lines_init(START_FLAGS);
            }, 100);
        }
        
    }
    
    textareas[i].addEventListener("keyup", function (e)
    {
        if(this.selectionStart == this.selectionEnd && !global_autocompletion_running && e.keyCode!=8 && e.which!=8 && (!e.ctrlKey || e.altKey) && (e.keyCode < 37 || e.keyCode > 40) && (e.which < 37 || e.which > 40) && (e.keyCode < 16 || e.keyCode > 18) && (e.which < 16 || e.which > 18))
        {
            let c = this.value[this.selectionStart-1];
            if(c == '"' || c == "'" || c == '(' || c == '[' || c == '{')
            {
                if(c == '(')
                    c = ')';
                else if(c == '[')
                    c = ']';
                else if(c == '{')
                    c = '}';
                if([undefined, '\n', ';', ',', ')', ']', '}', ' ', '=', '+', '.'].includes(this.value[this.selectionStart]))
                {
                    if(c != '"' && c != "'" || !is_char_of_variable(this.value[this.selectionStart-2]))
                    {
                        let s = this.selectionStart;
                        this.value = this.value.substring(0, this.selectionStart) + c + this.value.substring(this.selectionEnd);
                        this.selectionStart = this.selectionEnd = s;
                    }
                }
                else if(c == '"' || c == "'")
                {
                    if(c == this.value[this.selectionStart])
                    {
                        let s = this.selectionStart;
                        this.value = this.value.substring(0, this.selectionStart-1) + this.value.substring(this.selectionEnd);
                        this.selectionStart = this.selectionEnd = s;
                    }
                }
                
            }
            else if(c == ')' || c == ']' || c == '}')
            {
                if(c == this.value[this.selectionStart])
                {
                    let s = this.selectionStart;
                    this.value = this.value.substring(0, this.selectionStart-1) + this.value.substring(this.selectionEnd);
                    this.selectionStart = this.selectionEnd = s;
                }
            }
        }
    });
     
}

function CancelEvent(e)
{
    if(e)
    {
        e.stopPropagation();
        e.preventDefault();
    }
    if(window.event)
    {
        window.event.cancelBubble = true;
        window.event.returnValue  = false;
        return;
    }
}