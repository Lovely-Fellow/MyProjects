import React from 'react'
import PropTypes from 'prop-types'

class Todo extends React.Component
{
   render() {
    const { onClick, completed, text, onTodoEditClick, editing} = this.props;
    
    return (

      <li
        onClick={()=>onClick({text})}
      >
        
        <span style={{textDecoration: editing?  'underline' : 'none'}}>{text}
        </span>
        
        {completed? 
            <button type="submit" 
            className={'float-right'}
            onClick={()=>onTodoEditClick(text)}>
              Edit
            </button>:""
        }
      </li>
    )
  }
}
Todo.propTypes = {
  onClick: PropTypes.func.isRequired,
  completed: PropTypes.bool.isRequired,
  text: PropTypes.string.isRequired,
  onTodoEditClick: PropTypes.func.isRequired
}

export default Todo
