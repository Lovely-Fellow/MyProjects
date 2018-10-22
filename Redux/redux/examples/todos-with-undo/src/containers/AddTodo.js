import React from 'react'
import { connect } from 'react-redux'
import { addTodo, saveTodo } from '../actions'

let AddTodo = ({ dispatch }) => {
  let input, inputid;
  let saving_id = 0;
  return (
    
    <div>
      <form onSubmit={e => {
        e.preventDefault()
        if (!input.value.trim()) {
          return
        }
        saving_id = Number.parseInt(inputid.value, 10);//!inputid.value.trim();
        if (!saving_id)
        {
          dispatch(addTodo(input.value))
        }
        else 
        {
         
          dispatch(saveTodo(saving_id, input.value))
        }
        if (!saving_id)
          input.value = ''
      }}>
        <input ref={node => {
          input = node
        }} />
        
        <input ref={node => {
          inputid = node
        }} />

        <button type="submit">
          {!saving_id ? "Add Todo" : "Save Todo"}
        </button>

      </form>
    </div>
  )
}
AddTodo = connect()(AddTodo)

export default AddTodo
