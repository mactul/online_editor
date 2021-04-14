global_words = [];
global_active_suggestion = false;
global_suggestions = [];
global_disabled_suggestions = false;

function edit_words(string)
{
    let i = 0;
    global_words = [];
    while(i < string.length)
    {
        let word = "";
        while(i < string.length && is_char_of_variable(string[i]))
        {
            word += string[i];
            i++;
        }
        if(word.length > 3 && !global_words.includes(word))
        {
            global_words = global_words.concat([word]);
        }
        i++;
    }
}


function selection_from_line_start(area)
{
    let cursor = area.selectionStart - 1;
    let i = 0;
    while(cursor >= 0 && area.value[cursor] != "\n")
    {
        cursor--;
        i++;
    }
    return i;
}

function get_cursor(textarea)
{
    const PADDING = 4;
    let width = textarea.clientWidth;
    let x = textarea.offsetLeft - 3 + PADDING + (selection_from_line_start(textarea) * 9.03) % (width - 2 * PADDING);
    
    let lines = textarea.value.substring(0, textarea.selectionStart).split("\n");
    let nb_lines = 0;
    for(let i = 0; i < lines.length; i++)
    {
        nb_lines += 1 + Math.trunc((lines[i].length * 9.03) / (width - 2 * PADDING));
        if(lines[i].length != 0 && Math.trunc((lines[i].length * 9.03) % (width - 2 * PADDING)) == 0)
        {
            nb_lines -= 1;
        }
        
    }
    nb_lines--;
    
    let y = nb_lines * 18 + textarea.offsetTop;
    return [x, y];
}

function insert_suggestion(string, start, end, new_cursor_pos)
{
    let textarea = document.getElementById("code_area");
    textarea.value = textarea.value.substring(0, start) + string + textarea.value.substring(end);
    textarea.selectionStart = new_cursor_pos;
    textarea.selectionEnd = new_cursor_pos;
    textarea.focus();
    document.getElementById("suggestions").style.display = "none";
}

function suggest()
{
    if(!global_disabled_suggestions)
    {
        let textarea = document.getElementById("code_area");
        
        let a = textarea.selectionStart;
        let i = a;
        while(is_char_of_variable(textarea.value[i-1]))
        {
            i--;
        }
        while(is_char_of_variable(textarea.value[a]))
        {
            a++;
        }
        
        let cursor = get_cursor(textarea);
        let suggestions = document.getElementById("suggestions");
        suggestions.style.left = (cursor[0] + 20) + "px";
        suggestions.style.top = (cursor[1] - 1) + "px";
        
        let out = "";
        let text = textarea.value.substring(i, a);
        if(text!="")
        {
            global_suggestions = [];
            for(let count=0; count<global_words.length; count++)
            {
                if(global_words[count].includes(text) && global_words[count]!=text)
                {
                    let word_to_display = global_words[count];
                    if(["elif", "if", "else", "switch", "case", "default", "from", "import", "include", "foreach", "del", "async", "await", "def", "function", "class", "while", "for", "in", "or", "and", "not", "is", "as", "with", "undefined", "break", "pass", "continue", "return", "try", "except", "raise", "catch", "global", "extern", "assert", "finally", "lambda", "yield", "do", "goto"].includes(word_to_display) || ["none", "false", "true", "null"].includes(word_to_display.toLowerCase()))
                    {
                        word_to_display = "<keyword>"+word_to_display+"</keyword>";
                    }
                    
                    if(global_active_suggestion === false || !global_active_suggestion.includes(text) || global_active_suggestion == text)
                    {
                        global_active_suggestion = global_words[count];
                    }
                    
                    if(global_words[count] == global_active_suggestion)
                    {
                        out += "<div class='active' onclick=insert_suggestion('"+global_words[count]+"',"+i+","+a+","+(global_words[count].length + i)+")>"+word_to_display+"</div>";
                    }
                    else
                    {
                        out += "<div onclick=insert_suggestion('"+global_words[count]+"',"+i+","+a+","+(a + global_words[count].length - text.length)+")>"+word_to_display+"</div>";
                    }
                    global_suggestions = global_suggestions.concat([global_words[count]]);
                }
            }
        }
        suggestions.innerHTML = out;
        if(out == "")
        {
            suggestions.style.display = "none";
            global_active_suggestion = false;
            global_suggestions = [];
        }
        else
        {
            suggestions.style.display = "block";
        }
    }
}

document.getElementById("code_area").onkeyup = function() {suggest()};
document.getElementById("code_area").onclick = function() {global_disabled_suggestions = false; suggest()};