import React from 'react'
import PropTypes from 'prop-types'
import Todo from './Todo'

const TodoList = ({ todos, onTodoClick, onTodoEditClick}) => (
  
  
  <ul>
    {todos.map(todo =>
      <Todo
        key={todo.id}
        {...todo}
        onClick={() => onTodoClick(todo.id)}
        onTodoEditClick={(param) => onTodoEditClick(todo.id,param)}
      />
      )
    }
  </ul>
)

TodoList.propTypes = {
  todos: PropTypes.arrayOf(PropTypes.shape({
    id: PropTypes.number.isRequired,
    completed: PropTypes.bool.isRequired,
    text: PropTypes.string.isRequired,
    editing: PropTypes.number.isRequired,
    saving: PropTypes.number.isRequired
  }).isRequired).isRequired,
  onTodoClick: PropTypes.func.isRequired,
  onTodoEditClick:  PropTypes.func.isRequired,
  onSaveTodoClick:  PropTypes.func.isRequired
}

export default TodoList
