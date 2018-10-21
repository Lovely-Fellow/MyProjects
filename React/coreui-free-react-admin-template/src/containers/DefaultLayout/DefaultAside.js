import React, { Component } from "react";
import {
  Nav,
  NavItem,
  NavLink,
  Progress,
  TabContent,
  TabPane,
  ListGroup,
  ListGroupItem,
} from "reactstrap";
import PropTypes from "prop-types";
import classNames from "classnames";
import { AppSwitch } from "@coreui/react";
import { Badge, Card, CardBody, CardHeader, Pagination, PaginationItem, PaginationLink, Table } from 'reactstrap';

const propTypes = {
  children: PropTypes.node
};

const defaultProps = {};


class DefaultAside extends Component {
  constructor(props) {
    super(props);

    this.toggle = this.toggle.bind(this);
    this.state = {
      activeTab: "1",
      bCardTable: 1
    };
    this.ChangeCardTable=this.ChangeCardTable.bind(this);
  }

  toggle(tab) {
    if (this.state.activeTab !== tab) {
      this.setState({
        activeTab: tab
      });
    }
  }


  ChangeCardTable(e)
  {
      this.setState({bCardTable: !this.state.bCardTable});
  }
 

  render() {
    // eslint-disable-next-line
    const { children, ...attributes } = this.props;

    let card_table;
    if (this.state.bCardTable)
    {
      card_table = (
        <Card>
          <CardHeader>
            Card with label
            <Badge color="success" className="float-right">Success</Badge>
          </CardHeader>
          <CardBody>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
            laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation
            ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.
          </CardBody>
        </Card>            
      );
    }
    else{
       card_table = (
              <Card>
                <CardHeader>
                  <i className="fa fa-align-justify"></i> Simple Table
                </CardHeader>
                <CardBody>
                  <Table responsive>
                    <thead>
                    <tr>
                      <th>Username</th>
                      <th>Date registered</th>
                      <th>Role</th>
                      <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                      <td>Samppa Nori</td>
                      <td>2012/01/01</td>
                      <td>Member</td>
                      <td>
                        <Badge color="success">Active</Badge>
                      </td>
                    </tr>
                    <tr>
                      <td>Estavan Lykos</td>
                      <td>2012/02/01</td>
                      <td>Staff</td>
                      <td>
                        <Badge color="danger">Banned</Badge>
                      </td>
                    </tr>
                    <tr>
                      <td>Chetan Mohamed</td>
                      <td>2012/02/01</td>
                      <td>Admin</td>
                      <td>
                        <Badge color="secondary">Inactive</Badge>
                      </td>
                    </tr>
                    <tr>
                      <td>Derick Maximinus</td>
                      <td>2012/03/01</td>
                      <td>Member</td>
                      <td>
                        <Badge color="warning">Pending</Badge>
                      </td>
                    </tr>
                    <tr>
                      <td>Friderik Dávid</td>
                      <td>2012/01/21</td>
                      <td>Staff</td>
                      <td>
                        <Badge color="success">Active</Badge>
                      </td>
                    </tr>
                    </tbody>
                  </Table>
                  <Pagination size={"sm"}>
                    <PaginationItem>
                      <PaginationLink previous tag="button"></PaginationLink>
                    </PaginationItem>
                    <PaginationItem active>
                      <PaginationLink tag="button">1</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink tag="button">2</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink tag="button">3</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink tag="button">4</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink next tag="button"></PaginationLink>
                    </PaginationItem>
                  </Pagination>
                </CardBody>
              </Card>
        );
    }

    return (
      <React.Fragment>
        <Nav tabs>
          <NavItem>
            <NavLink
              className={classNames({ active: this.state.activeTab === "1" })}
              onClick={() => {
                this.toggle("1");
              }}
            >
              <i className="icon-list" />
            </NavLink>
          </NavItem>
          <NavItem>
            <NavLink
              className={classNames({ active: this.state.activeTab === "2" })}
              onClick={() => {
                this.toggle("2");
              }}
            >
              <i className="icon-speech" />
            </NavLink>
          </NavItem>
          <NavItem>
            <NavLink
              className={classNames({ active: this.state.activeTab === "3" })}
              onClick={() => {
                this.toggle("3");
              }}
            >
              <i className="icon-settings" />
            </NavLink>
          </NavItem>
          <NavItem>
            <NavLink
              className={classNames({ active: this.state.activeTab === "4" })}
              onClick={() => {
                this.toggle("4");
              }}
            >
              <i className="fa fa-cubes fa-lg" />
            </NavLink>
          </NavItem>
        </Nav>
        <TabContent activeTab={this.state.activeTab}>
          <TabPane tabId="1">
            <ListGroup className="list-group-accent" tag={"div"}>
              <ListGroupItem className="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
                Today
              </ListGroupItem>
              <ListGroupItem
                action
                tag="a"
                href="#"
                className="list-group-item-accent-warning list-group-item-divider"
              >
                <div className="avatar float-right">
                  <img
                    className="img-avatar"
                    src="assets/img/avatars/7.jpg"
                    alt="admin@bootstrapmaster.com"
                  />
                </div>
                <div>
                  Meeting with <strong>Lucas</strong>{" "}
                </div>
                <small className="text-muted mr-3">
                  <i className="icon-calendar" />
                  &nbsp; 1 - 3pm
                </small>
                <small className="text-muted">
                  <i className="icon-location-pin" /> Palo Alto, CA
                </small>
              </ListGroupItem>
              <ListGroupItem
                action
                tag="a"
                href="#"
                className="list-group-item-accent-info list-group-item-divider"
              >
                <div className="avatar float-right">
                  <img
                    className="img-avatar"
                    src="assets/img/avatars/4.jpg"
                    alt="admin@bootstrapmaster.com"
                  />
                </div>
                <div>
                  Skype with <strong>Megan</strong>
                </div>
                <small className="text-muted mr-3">
                  <i className="icon-calendar" />
                  &nbsp; 4 - 5pm
                </small>
                <small className="text-muted">
                  <i className="icon-social-skype" /> On-line
                </small>
              </ListGroupItem>
              <ListGroupItem className="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
                Tomorrow
              </ListGroupItem>
              <ListGroupItem
                action
                tag="a"
                href="#"
                className="list-group-item-accent-danger list-group-item-divider"
              >
                <div>
                  New UI Project - <strong>deadline</strong>
                </div>
                <small className="text-muted mr-3">
                  <i className="icon-calendar" />
                  &nbsp; 10 - 11pm
                </small>
                <small className="text-muted">
                  <i className="icon-home" />
                  &nbsp; creativeLabs HQ
                </small>
                <div className="avatars-stack mt-2">
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/2.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/3.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/4.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/5.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/6.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                </div>
              </ListGroupItem>
              <ListGroupItem
                action
                tag="a"
                href="#"
                className="list-group-item-accent-success list-group-item-divider"
              >
                <div>
                  <strong>#10 Startups.Garden</strong> Meetup
                </div>
                <small className="text-muted mr-3">
                  <i className="icon-calendar" />
                  &nbsp; 1 - 3pm
                </small>
                <small className="text-muted">
                  <i className="icon-location-pin" />
                  &nbsp; Palo Alto, CA
                </small>
              </ListGroupItem>
              <ListGroupItem
                action
                tag="a"
                href="#"
                className="list-group-item-accent-primary list-group-item-divider"
              >
                <div>
                  <strong>Team meeting</strong>
                </div>
                <small className="text-muted mr-3">
                  <i className="icon-calendar" />
                  &nbsp; 4 - 6pm
                </small>
                <small className="text-muted">
                  <i className="icon-home" />
                  &nbsp; creativeLabs HQ
                </small>
                <div className="avatars-stack mt-2">
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/2.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/3.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/4.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/5.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/6.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/7.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                  <div className="avatar avatar-xs">
                    <img
                      src={"assets/img/avatars/8.jpg"}
                      className="img-avatar"
                      alt="admin@bootstrapmaster.com"
                    />
                  </div>
                </div>
              </ListGroupItem>
            </ListGroup>
          </TabPane>
          <TabPane tabId="2" className="p-3">
            <div className="message">
              <div className="py-3 pb-5 mr-3 float-left">
                <div className="avatar">
                  <img
                    src={"assets/img/avatars/7.jpg"}
                    className="img-avatar"
                    alt="admin@bootstrapmaster.com"
                  />
                  <span className="avatar-status badge-success" />
                </div>
              </div>
              <div>
                <small className="text-muted">Lukasz Holeczek</small>
                <small className="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div className="text-truncate font-weight-bold">
                Lorem ipsum dolor sit amet
              </div>
              <small className="text-muted">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                eiusmod tempor incididunt...
              </small>
            </div>
            <hr />
            <div className="message">
              <div className="py-3 pb-5 mr-3 float-left">
                <div className="avatar">
                  <img
                    src={"assets/img/avatars/7.jpg"}
                    className="img-avatar"
                    alt="admin@bootstrapmaster.com"
                  />
                  <span className="avatar-status badge-success" />
                </div>
              </div>
              <div>
                <small className="text-muted">Lukasz Holeczek</small>
                <small className="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div className="text-truncate font-weight-bold">
                Lorem ipsum dolor sit amet
              </div>
              <small className="text-muted">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                eiusmod tempor incididunt...
              </small>
            </div>
            <hr />
            <div className="message">
              <div className="py-3 pb-5 mr-3 float-left">
                <div className="avatar">
                  <img
                    src={"assets/img/avatars/7.jpg"}
                    className="img-avatar"
                    alt="admin@bootstrapmaster.com"
                  />
                  <span className="avatar-status badge-success" />
                </div>
              </div>
              <div>
                <small className="text-muted">Lukasz Holeczek</small>
                <small className="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div className="text-truncate font-weight-bold">
                Lorem ipsum dolor sit amet
              </div>
              <small className="text-muted">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                eiusmod tempor incididunt...
              </small>
            </div>
            <hr />
            <div className="message">
              <div className="py-3 pb-5 mr-3 float-left">
                <div className="avatar">
                  <img
                    src={"assets/img/avatars/7.jpg"}
                    className="img-avatar"
                    alt="admin@bootstrapmaster.com"
                  />
                  <span className="avatar-status badge-success" />
                </div>
              </div>
              <div>
                <small className="text-muted">Lukasz Holeczek</small>
                <small className="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div className="text-truncate font-weight-bold">
                Lorem ipsum dolor sit amet
              </div>
              <small className="text-muted">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                eiusmod tempor incididunt...
              </small>
            </div>
            <hr />
            <div className="message">
              <div className="py-3 pb-5 mr-3 float-left">
                <div className="avatar">
                  <img
                    src={"assets/img/avatars/7.jpg"}
                    className="img-avatar"
                    alt="admin@bootstrapmaster.com"
                  />
                  <span className="avatar-status badge-success" />
                </div>
              </div>
              <div>
                <small className="text-muted">Lukasz Holeczek</small>
                <small className="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div className="text-truncate font-weight-bold">
                Lorem ipsum dolor sit amet
              </div>
              <small className="text-muted">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                eiusmod tempor incididunt...
              </small>
            </div>
          </TabPane>
          <TabPane tabId="3" className="p-3">
            <h6>Settings</h6>

            <div className="aside-options">
              <div className="clearfix mt-4">
                <small>
                  <b>Option 1</b>
                </small>
                <AppSwitch
                  className={"float-right"}
                  variant={"pill"}
                  label
                  color={"success"}
                  defaultChecked
                  size={"sm"}
                />
              </div>
              <div>
                <small className="text-muted">
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                  do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </small>
              </div>
            </div>

            <div className="aside-options">
              <div className="clearfix mt-3">
                <small>
                  <b>Option 2</b>
                </small>
                <AppSwitch
                  className={"float-right"}
                  variant={"pill"}
                  label
                  color={"success"}
                  size={"sm"}
                  
                />
              </div>
              <div>
                <small className="text-muted">
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                  do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </small>
              </div>
            </div>

            <div className="aside-options">
              <div className="clearfix mt-3">
                <small>
                  <b>Option 3</b>
                </small>
                <AppSwitch
                  className={"float-right"}
                  variant={"pill"}
                  label
                  color={"success"}
                  defaultChecked
                  size={"sm"}
                  disabled
                />
                <div>
                  <small className="text-muted">Option disabled.</small>
                </div>
              </div>
            </div>

            <div className="aside-options">
              <div className="clearfix mt-3">
                <small>
                  <b>Option 4</b>
                </small>
                <AppSwitch
                  className={"float-right"}
                  variant={"pill"}
                  label
                  color={"success"}
                  defaultChecked
                  size={"sm"}
                />
              </div>
            </div>

            <hr />
            <h6>System Utilization</h6>

            <div className="text-uppercase mb-1 mt-4">
              <small>
                <b>CPU Usage</b>
              </small>
            </div>
            <Progress className="progress-xs" color="info" value="25" />
            <small className="text-muted">348 Processes. 1/4 Cores.</small>

            <div className="text-uppercase mb-1 mt-2">
              <small>
                <b>Memory Usage</b>
              </small>
            </div>
            <Progress className="progress-xs" color="warning" value="70" />
            <small className="text-muted">11444GB/16384MB</small>

            <div className="text-uppercase mb-1 mt-2">
              <small>
                <b>SSD 1 Usage</b>
              </small>
            </div>
            <Progress className="progress-xs" color="danger" value="95" />
            <small className="text-muted">243GB/256GB</small>

            <div className="text-uppercase mb-1 mt-2">
              <small>
                <b>SSD 2 Usage</b>
              </small>
            </div>
            <Progress className="progress-xs" color="success" value="10" />
            <small className="text-muted">25GB/256GB</small>
          </TabPane>
          <TabPane tabId="4" className="p-4">
            <h6>Switch Card/Table</h6>

            <div className="aside-options">
              <div className="clearfix mt-4">
                <small>
                  <b>Switch</b>
                </small>
                <AppSwitch 
                  className={'float-right'} 
                  color={'warning'}
                  defaultChecked 
                  size={"sm"}
                  onClick={this.ChangeCardTable}
                />
              </div>
              <div className="clearfix mt-4">
                {card_table}
              </div>


            </div>
            
          </TabPane>
        </TabContent>
        
      </React.Fragment>
    );
  }
}

DefaultAside.propTypes = propTypes;
DefaultAside.defaultProps = defaultProps;

export default DefaultAside;
