import React from 'react'
import PropTypes from 'prop-types'

const Todo = ({ onClick1, completed, text, onToEditClick}) => (

  <li
    onClick={onClick1}
  >
    {text}
    {completed? 
        <button type="submit" className={'float-right'} onClick={()=>onToEditClick({text})}>
          Edit
        </button>:""
    }
  </li>
)

Todo.propTypes = {
  onClick: PropTypes.func.isRequired,
  completed: PropTypes.bool.isRequired,
  text: PropTypes.string.isRequired
}

export default Todo
