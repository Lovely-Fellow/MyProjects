import React from 'react'
import { connect } from 'react-redux'
import { addTodo, saveTodo } from '../actions'

let AddTodo = ({ dispatch, props, state }) => {
  let input
  let issaving = 0;
  return (
    
    <div>
      <form onSubmit={e => {
        e.preventDefault()
        if (!input.value.trim()) {
          return
        }
  
        if (!issaving)
        {
          dispatch(addTodo(input.value))
        }
        else 
        {
          dispatch(saveTodo(1, input.value))
        }
        input.value = ''
      }}>
        <input ref={node => {
          input = node
        }} />
        
        <button type="submit">
          {!issaving ? "Add Todo" : "Save Todo"}
        </button>

        
      </form>
    </div>
  )
}
AddTodo = connect()(AddTodo)

export default AddTodo
