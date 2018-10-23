import React from 'react'
import { connect } from 'react-redux'
import * as TodoActions  from '../actions'

class AddTodo extends React.Component {
  render()
  {
    const {todos} = this.props;
    let input, text;
    let saving = 0;
    let editing = 0;
    let id = -1;
    let form;
    console.log("Pass here");
 
    todos.map(todo=>
    {
      if ( !editing && !saving)
      {
        editing = todo.editing;
        saving = todo.saving;
        if (editing || saving)
        {
          text = todo.text;
          id = todo.id;
        }
      }
    });
 
    if (editing) {
      form = (
        <div>
          <form onSubmit={e => {
            e.preventDefault()
            if (!input.value.trim()) {
              return
            }
        
            console.log("Pass here1 ")
              this.props.editTodo(-1,"");
              this.props.saveTodo(id, input.value, 1);
           }}>
      
            <input ref={node => {
              input = node
              if (input!==null)
               input.value = text
            }}/>
          
         
            <button type="submit">
              Save Todo
            </button>
          </form>
        </div>
      )
    }
    else 
    {if (saving)
    {
      form = (
        <div>
          <form onSubmit={e => {
            e.preventDefault()
            if (!input.value.trim()) {
              return
            }
            console.log(input.value)
            this.props.saveTodo(-1,"");
            this.props.addTodo(input.value)
          }}>
      
          <input ref={node => {
              input = node
              if (input!==null)
              input.value = ''
            }}/>
        
            <button type="submit">
              Add Todo
            </button>
          </form>
        </div>
      )
    }
    else
    {
      form = (
        <div>
          <form onSubmit={e => {
            e.preventDefault()
            if (!input.value.trim()) {
              return
            }
            this.props.addTodo(input.value)
          }}>
           <input ref={node => {
              input = node
            }}/>
           <button type="submit">
              Add Todo
            </button>
         </form>
        </div>
      )
    }
  }
    return (
      form
    )
  }
}

const mapStateToProps = (state) => ({
  todos: state.todos.present,
})

const mapDispatchtoProps = (dispatch) =>
({
  addTodo:(text) => dispatch(TodoActions.addTodo(text)),
  saveTodo:(id, text, saving) => dispatch(TodoActions.saveTodo(id, text, saving)),
  editTodo:(id, text) => dispatch(TodoActions.editTodo(id, text))
})

AddTodo = connect(mapStateToProps, mapDispatchtoProps)(AddTodo)


export default AddTodo

